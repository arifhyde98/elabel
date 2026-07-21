<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SertifikatBoxModel;
use App\Models\SertifikatModel;

class SertifikatBoxController extends BaseController
{
    private const MAX_SERTIFIKAT_PER_BOX = 40;

    private SertifikatBoxModel $boxes;
    private SertifikatModel $sertifikat;

    public function __construct()
    {
        helper(['form']);
        $this->boxes = new SertifikatBoxModel();
        $this->sertifikat = new SertifikatModel();
    }

    public function index(): string
    {
        $boxes = $this->boxes
            ->select('sertifikat_boxes.*, COUNT(sertifikat_tanah.id) AS sertifikat_count')
            ->join('sertifikat_tanah', 'sertifikat_tanah.box_id = sertifikat_boxes.id', 'left')
            ->groupBy('sertifikat_boxes.id')
            ->orderBy('sertifikat_boxes.id', 'desc')
            ->findAll();

        return view('admin/sertifikat_boxes/index', [
            'boxes' => $boxes,
            'maxPerBox' => self::MAX_SERTIFIKAT_PER_BOX,
            'activeMenu' => 'sertifikat_boxes',
        ]);
    }

    public function create(): string
    {
        return view('admin/sertifikat_boxes/create', [
            'nextBoxCode' => $this->nextSequentialBoxCode(),
            'activeMenu' => 'sertifikat_boxes',
        ]);
    }

    public function store()
    {
        $rules = [
            'box_code' => 'required|max_length[30]',
            'lokasi'   => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $lokasi = trim((string) $this->request->getPost('lokasi'));
        $existingByLocation = $this->findBoxesByLocation($lokasi);

        $boxCode = $existingByLocation !== []
            ? $this->nextBoxCodeSuffix((string) ($existingByLocation[0]['box_code'] ?? ''))
            : $this->normalizeBoxCode((string) $this->request->getPost('box_code'));

        if ($this->boxes->where('box_code', $boxCode)->first() !== null) {
            return redirect()->back()->withInput()->with('error', 'Kode box sertipikat sudah digunakan.');
        }

        $this->boxes->insert([
            'box_code' => $boxCode,
            'lokasi' => $lokasi,
            'created_by' => session()->get('user_id') ? (int) session()->get('user_id') : null,
        ]);

        return redirect()->to(site_url('admin/sertifikat-boxes'))->with('success', 'Box sertipikat berhasil ditambahkan.');
    }

    public function show(int $id)
    {
        $box = $this->boxes->find($id);
        if ($box === null) {
            return redirect()->to(site_url('admin/sertifikat-boxes'))->with('error', 'Box sertipikat tidak ditemukan.');
        }

        $items = $this->sertifikat
            ->where('box_id', $id)
            ->orderBy('no_sertipikat', 'asc')
            ->findAll();

        $mergeCandidateCount = count($items);
        $mergeTargets = $mergeCandidateCount > 0
            ? $this->availableMergeTargets($id, $mergeCandidateCount)
            : [];
        $splitOptions = $this->availableSplitOptions($box, $items);

        return view('admin/sertifikat_boxes/show', [
            'box' => $box,
            'items' => $items,
            'maxPerBox' => self::MAX_SERTIFIKAT_PER_BOX,
            'mergeCandidateCount' => $mergeCandidateCount,
            'mergeTargets' => $mergeTargets,
            'splitOptions' => $splitOptions,
            'activeMenu' => 'sertifikat_boxes',
        ]);
    }

    public function merge(int $id)
    {
        $sourceBox = $this->boxes->find($id);
        if ($sourceBox === null) {
            return redirect()->to(site_url('admin/sertifikat-boxes'))->with('error', 'Box sertipikat sumber tidak ditemukan.');
        }

        $targetId = (int) $this->request->getPost('target_box_id');
        $targetBox = $this->boxes->find($targetId);
        if ($targetBox === null) {
            return redirect()->to(site_url('admin/sertifikat-boxes/' . $id))->with('error', 'Box sertipikat tujuan tidak ditemukan.');
        }

        if ($targetId === $id) {
            return redirect()->to(site_url('admin/sertifikat-boxes/' . $id))->with('error', 'Box tujuan tidak boleh sama dengan box sumber.');
        }

        $sourceCount = $this->countSertifikatByBox($id);
        $targetCount = $this->countSertifikatByBox($targetId);
        if ($sourceCount === 0) {
            return redirect()->to(site_url('admin/sertifikat-boxes/' . $id))->with('error', 'Box sumber tidak memiliki data sertipikat untuk digabung.');
        }

        if (($sourceCount + $targetCount) > self::MAX_SERTIFIKAT_PER_BOX) {
            return redirect()->to(site_url('admin/sertifikat-boxes/' . $id))->with('error', 'Penggabungan hanya bisa dilakukan jika total isi box sumber dan tujuan maksimal ' . self::MAX_SERTIFIKAT_PER_BOX . ' sertipikat.');
        }

        $mergedLocation = $this->mergeLocationLabels(
            (string) ($targetBox['lokasi'] ?? ''),
            (string) ($sourceBox['lokasi'] ?? '')
        );

        $db = $this->sertifikat->db;
        $db->transStart();

        $this->sertifikat
            ->where('box_id', $id)
            ->set(['box_id' => $targetId])
            ->update();

        $this->boxes->update($targetId, [
            'lokasi' => $mergedLocation,
        ]);

        $this->boxes->delete($id);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to(site_url('admin/sertifikat-boxes/' . $id))->with('error', 'Penggabungan box sertipikat gagal disimpan.');
        }

        return redirect()->to(site_url('admin/sertifikat-boxes/' . $targetId))->with('success', 'Box ' . $sourceBox['box_code'] . ' berhasil digabung ke box ' . $targetBox['box_code'] . '.');
    }

    public function split(int $id)
    {
        $box = $this->boxes->find($id);
        if ($box === null) {
            return redirect()->to(site_url('admin/sertifikat-boxes'))->with('error', 'Box sertipikat tidak ditemukan.');
        }

        $selectedLocation = trim((string) $this->request->getPost('split_location'));
        if ($selectedLocation === '') {
            return redirect()->to(site_url('admin/sertifikat-boxes/' . $id))->with('error', 'Pilih lokasi yang ingin dipisahkan.');
        }

        $allLocations = $this->explodeLocationLabels((string) ($box['lokasi'] ?? ''));
        if (count($allLocations) < 2) {
            return redirect()->to(site_url('admin/sertifikat-boxes/' . $id))->with('error', 'Box ini belum merupakan box gabungan.');
        }

        $selectedKey = strtoupper($selectedLocation);
        if (! isset($allLocations[$selectedKey])) {
            return redirect()->to(site_url('admin/sertifikat-boxes/' . $id))->with('error', 'Lokasi yang dipilih tidak ada pada box ini.');
        }

        $selectedLabel = $allLocations[$selectedKey];
        unset($allLocations[$selectedKey]);
        if ($allLocations === []) {
            return redirect()->to(site_url('admin/sertifikat-boxes/' . $id))->with('error', 'Minimal harus ada satu lokasi yang tersisa di box asal.');
        }

        $itemsToMove = $this->findSertifikatItemsByLocationInBox($id, $selectedLabel);
        if ($itemsToMove === []) {
            return redirect()->to(site_url('admin/sertifikat-boxes/' . $id))->with('error', 'Tidak ada data sertipikat untuk lokasi yang dipilih.');
        }

        $newBoxCode = $this->nextBoxCodeSuffix((string) ($box['box_code'] ?? ''));
        $remainingLocation = implode(', ', array_values($allLocations));

        $db = $this->sertifikat->db;
        $db->transStart();

        $newBoxId = $this->boxes->insert([
            'box_code' => $newBoxCode,
            'lokasi' => $selectedLabel,
            'created_by' => session()->get('user_id') ? (int) session()->get('user_id') : null,
        ], true);

        foreach ($itemsToMove as $item) {
            $this->sertifikat->update((int) $item['id'], [
                'box_id' => (int) $newBoxId,
            ]);
        }

        $this->boxes->update($id, [
            'lokasi' => $remainingLocation,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to(site_url('admin/sertifikat-boxes/' . $id))->with('error', 'Pemisahan box sertipikat gagal disimpan.');
        }

        return redirect()->to(site_url('admin/sertifikat-boxes/' . $newBoxId))->with('success', 'Lokasi ' . $selectedLabel . ' berhasil dipisahkan ke box ' . $newBoxCode . '.');
    }

    public function label(int $id)
    {
        $box = $this->boxes->find($id);
        if ($box === null) {
            return redirect()->to(site_url('admin/sertifikat-boxes'))->with('error', 'Box sertipikat tidak ditemukan.');
        }

        $items = $this->sertifikat
            ->where('box_id', $id)
            ->orderBy('no_sertipikat', 'asc')
            ->findAll();

        return view('admin/sertifikat_boxes/label', [
            'box' => $box,
            'items' => $items,
            'maxPerBox' => self::MAX_SERTIFIKAT_PER_BOX,
        ]);
    }

    public function delete(int $id)
    {
        $box = $this->boxes->find($id);
        if ($box === null) {
            return redirect()->to(site_url('admin/sertifikat-boxes'))->with('error', 'Box sertipikat tidak ditemukan.');
        }

        $count = $this->sertifikat->where('box_id', $id)->countAllResults();
        if ($count > 0) {
            return redirect()->to(site_url('admin/sertifikat-boxes'))->with('error', 'Box sertipikat tidak bisa dihapus karena masih berisi data.');
        }

        $this->boxes->delete($id);

        return redirect()->to(site_url('admin/sertifikat-boxes'))->with('success', 'Box sertipikat berhasil dihapus.');
    }

    private function normalizeBoxCode(string $code): string
    {
        $code = strtoupper(trim($code));

        if ($code === '') {
            return $this->nextSequentialBoxCode();
        }

        if (strpos($code, 'ST-') === 0) {
            $suffix = substr($code, 3);
            if (ctype_digit($suffix)) {
                return 'ST-' . str_pad($suffix, 2, '0', STR_PAD_LEFT);
            }

            return $code;
        }

        if (ctype_digit($code)) {
            return 'ST-' . str_pad($code, 2, '0', STR_PAD_LEFT);
        }

        return 'ST-' . $code;
    }

    private function nextSequentialBoxCode(): string
    {
        $rows = $this->boxes
            ->select('box_code')
            ->like('box_code', 'ST-', 'after')
            ->findAll();

        $maxNumber = 0;
        foreach ($rows as $row) {
            $boxCode = (string) ($row['box_code'] ?? '');
            if (preg_match('/^ST-(\d+)$/', $boxCode, $matches) !== 1) {
                continue;
            }

            $number = (int) $matches[1];
            if ($number > $maxNumber) {
                $maxNumber = $number;
            }
        }

        return 'ST-' . str_pad((string) ($maxNumber + 1), 2, '0', STR_PAD_LEFT);
    }

    private function nextBoxCodeSuffix(string $baseCode): string
    {
        $baseCode = preg_replace('/ \(\d+\)$/', '', trim($baseCode)) ?? trim($baseCode);
        $existing = $this->boxes
            ->select('box_code')
            ->like('box_code', $baseCode, 'after')
            ->orderBy('box_code', 'asc')
            ->findAll();

        $max = 1;
        foreach ($existing as $row) {
            $code = (string) ($row['box_code'] ?? '');
            if (preg_match('/^' . preg_quote($baseCode, '/') . ' \((\d+)\)$/', $code, $matches) === 1) {
                $max = max($max, (int) $matches[1]);
                continue;
            }

            if ($code === $baseCode) {
                $max = max($max, 1);
            }
        }

        return $baseCode . ' (' . ($max + 1) . ')';
    }

    private function findBoxesByLocation(string $lokasi): array
    {
        $normalized = strtoupper(trim($lokasi));
        if ($normalized === '') {
            return [];
        }

        return array_values(array_filter(
            $this->boxes->orderBy('id', 'asc')->findAll(),
            static function (array $box) use ($normalized): bool {
                $boxLocations = array_map(
                    static fn (string $value): string => strtoupper(trim($value)),
                    preg_split('/\s*,\s*/', (string) ($box['lokasi'] ?? '')) ?: []
                );

                return in_array($normalized, array_filter($boxLocations), true);
            }
        ));
    }

    private function countSertifikatByBox(int $boxId): int
    {
        return $this->sertifikat->where('box_id', $boxId)->countAllResults();
    }

    private function availableMergeTargets(int $sourceId, int $sourceCount): array
    {
        $targets = $this->boxes
            ->where('id !=', $sourceId)
            ->orderBy('box_code', 'asc')
            ->findAll();

        if ($targets === []) {
            return [];
        }

        $result = [];
        foreach ($targets as $target) {
            $targetCount = $this->countSertifikatByBox((int) $target['id']);
            if (($sourceCount + $targetCount) > self::MAX_SERTIFIKAT_PER_BOX) {
                continue;
            }

            $result[] = $target + [
                'sertifikat_count' => $targetCount,
                'combined_count' => $sourceCount + $targetCount,
            ];
        }

        return $result;
    }

    private function mergeLocationLabels(string $targetLocation, string $sourceLocation): string
    {
        $parts = [];
        foreach ([$targetLocation, $sourceLocation] as $location) {
            $items = preg_split('/\s*,\s*/', trim($location)) ?: [];
            foreach ($items as $item) {
                $item = trim($item);
                if ($item === '') {
                    continue;
                }

                $key = strtoupper($item);
                $parts[$key] = $item;
            }
        }

        return implode(', ', array_values($parts));
    }

    private function availableSplitOptions(array $box, array $items): array
    {
        $locations = $this->explodeLocationLabels((string) ($box['lokasi'] ?? ''));
        if (count($locations) < 2) {
            return [];
        }

        $counts = [];
        foreach ($items as $item) {
            $label = trim((string) ($item['lokasi'] ?? ''));
            if ($label === '') {
                continue;
            }

            $counts[strtoupper($label)] = ($counts[strtoupper($label)] ?? 0) + 1;
        }

        $options = [];
        foreach ($locations as $key => $label) {
            $options[] = [
                'label' => $label,
                'count' => $counts[$key] ?? 0,
            ];
        }

        return $options;
    }

    private function explodeLocationLabels(string $location): array
    {
        $parts = [];
        foreach (preg_split('/\s*,\s*/', trim($location)) ?: [] as $item) {
            $item = trim($item);
            if ($item === '') {
                continue;
            }

            $parts[strtoupper($item)] = $item;
        }

        return $parts;
    }

    private function findSertifikatItemsByLocationInBox(int $boxId, string $location): array
    {
        $items = $this->sertifikat
            ->where('box_id', $boxId)
            ->orderBy('id', 'asc')
            ->findAll();

        $normalized = strtoupper(trim($location));
        return array_values(array_filter($items, static function (array $item) use ($normalized): bool {
            return strtoupper(trim((string) ($item['lokasi'] ?? ''))) === $normalized;
        }));
    }
}
