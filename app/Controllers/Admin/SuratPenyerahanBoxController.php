<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SuratPenyerahanBoxModel;
use App\Models\SuratPenyerahanModel;

class SuratPenyerahanBoxController extends BaseController
{
    private const MAX_SURAT_PER_BOX = 40;

    private SuratPenyerahanBoxModel $boxes;
    private SuratPenyerahanModel $suratPenyerahan;

    public function __construct()
    {
        helper(['form']);
        $this->boxes = new SuratPenyerahanBoxModel();
        $this->suratPenyerahan = new SuratPenyerahanModel();
    }

    public function index(): string
    {
        $boxes = $this->boxes
            ->select('surat_penyerahan_boxes.*, COUNT(surat_penyerahan.id) AS surat_count')
            ->join('surat_penyerahan', 'surat_penyerahan.box_id = surat_penyerahan_boxes.id', 'left')
            ->groupBy('surat_penyerahan_boxes.id')
            ->orderBy('surat_penyerahan_boxes.id', 'desc')
            ->findAll();

        return view('admin/surat_penyerahan_boxes/index', [
            'boxes' => $boxes,
            'maxPerBox' => self::MAX_SURAT_PER_BOX,
            'activeMenu' => 'surat_penyerahan_boxes',
        ]);
    }

    public function create(): string
    {
        return view('admin/surat_penyerahan_boxes/create', [
            'nextBoxCode' => $this->nextSequentialBoxCode(),
            'activeMenu' => 'surat_penyerahan_boxes',
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
            return redirect()->back()->withInput()->with('error', 'Kode box surat penyerahan sudah digunakan.');
        }

        $this->boxes->insert([
            'box_code' => $boxCode,
            'lokasi' => $lokasi,
            'created_by' => session()->get('user_id') ? (int) session()->get('user_id') : null,
        ]);

        return redirect()->to(site_url('admin/surat-penyerahan-boxes'))->with('success', 'Box surat penyerahan berhasil ditambahkan.');
    }

    public function show(int $id)
    {
        $box = $this->boxes->find($id);
        if ($box === null) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes'))->with('error', 'Box surat penyerahan tidak ditemukan.');
        }

        $items = $this->suratPenyerahan
            ->where('box_id', $id)
            ->orderBy('no_surat', 'asc')
            ->findAll();

        $mergeCandidateCount = count($items);
        $mergeTargets = $mergeCandidateCount > 0
            ? $this->availableMergeTargets($id, $mergeCandidateCount)
            : [];
        $splitOptions = $this->availableSplitOptions($box, $items);

        return view('admin/surat_penyerahan_boxes/show', [
            'box' => $box,
            'items' => $items,
            'maxPerBox' => self::MAX_SURAT_PER_BOX,
            'mergeCandidateCount' => $mergeCandidateCount,
            'mergeTargets' => $mergeTargets,
            'splitOptions' => $splitOptions,
            'activeMenu' => 'surat_penyerahan_boxes',
        ]);
    }

    public function merge(int $id)
    {
        $sourceBox = $this->boxes->find($id);
        if ($sourceBox === null) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes'))->with('error', 'Box surat penyerahan sumber tidak ditemukan.');
        }

        $targetId = (int) $this->request->getPost('target_box_id');
        $targetBox = $this->boxes->find($targetId);
        if ($targetBox === null) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes/' . $id))->with('error', 'Box surat penyerahan tujuan tidak ditemukan.');
        }

        if ($targetId === $id) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes/' . $id))->with('error', 'Box tujuan tidak boleh sama dengan box sumber.');
        }

        $sourceCount = $this->countSuratByBox($id);
        $targetCount = $this->countSuratByBox($targetId);
        if ($sourceCount === 0) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes/' . $id))->with('error', 'Box sumber tidak memiliki data surat penyerahan untuk digabung.');
        }

        if (($sourceCount + $targetCount) > self::MAX_SURAT_PER_BOX) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes/' . $id))->with('error', 'Penggabungan hanya bisa dilakukan jika total isi box sumber dan tujuan maksimal ' . self::MAX_SURAT_PER_BOX . ' surat.');
        }

        $mergedLocation = $this->mergeLocationLabels(
            (string) ($targetBox['lokasi'] ?? ''),
            (string) ($sourceBox['lokasi'] ?? '')
        );

        $db = $this->suratPenyerahan->db;
        $db->transStart();

        $this->suratPenyerahan
            ->where('box_id', $id)
            ->set(['box_id' => $targetId])
            ->update();

        $this->boxes->update($targetId, [
            'lokasi' => $mergedLocation,
        ]);

        $this->boxes->delete($id);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes/' . $id))->with('error', 'Penggabungan box surat penyerahan gagal disimpan.');
        }

        return redirect()->to(site_url('admin/surat-penyerahan-boxes/' . $targetId))->with('success', 'Box ' . $sourceBox['box_code'] . ' berhasil digabung ke box ' . $targetBox['box_code'] . '.');
    }

    public function split(int $id)
    {
        $box = $this->boxes->find($id);
        if ($box === null) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes'))->with('error', 'Box surat penyerahan tidak ditemukan.');
        }

        $selectedLocation = trim((string) $this->request->getPost('split_location'));
        if ($selectedLocation === '') {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes/' . $id))->with('error', 'Pilih lokasi yang ingin dipisahkan.');
        }

        $allLocations = $this->explodeLocationLabels((string) ($box['lokasi'] ?? ''));
        if (count($allLocations) < 2) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes/' . $id))->with('error', 'Box ini belum merupakan box gabungan.');
        }

        $selectedKey = strtoupper($selectedLocation);
        if (! isset($allLocations[$selectedKey])) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes/' . $id))->with('error', 'Lokasi yang dipilih tidak ada pada box ini.');
        }

        $selectedLabel = $allLocations[$selectedKey];
        unset($allLocations[$selectedKey]);
        if ($allLocations === []) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes/' . $id))->with('error', 'Minimal harus ada satu lokasi yang tersisa di box asal.');
        }

        $itemsToMove = $this->findSuratItemsByLocationInBox($id, $selectedLabel);
        if ($itemsToMove === []) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes/' . $id))->with('error', 'Tidak ada data surat penyerahan untuk lokasi yang dipilih.');
        }

        $newBoxCode = $this->nextBoxCodeSuffix((string) ($box['box_code'] ?? ''));
        $remainingLocation = implode(', ', array_values($allLocations));

        $db = $this->suratPenyerahan->db;
        $db->transStart();

        $newBoxId = $this->boxes->insert([
            'box_code' => $newBoxCode,
            'lokasi' => $selectedLabel,
            'created_by' => session()->get('user_id') ? (int) session()->get('user_id') : null,
        ], true);

        foreach ($itemsToMove as $item) {
            $this->suratPenyerahan->update((int) $item['id'], [
                'box_id' => (int) $newBoxId,
            ]);
        }

        $this->boxes->update($id, [
            'lokasi' => $remainingLocation,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes/' . $id))->with('error', 'Pemisahan box surat penyerahan gagal disimpan.');
        }

        return redirect()->to(site_url('admin/surat-penyerahan-boxes/' . $newBoxId))->with('success', 'Lokasi ' . $selectedLabel . ' berhasil dipisahkan ke box ' . $newBoxCode . '.');
    }

    public function label(int $id)
    {
        $box = $this->boxes->find($id);
        if ($box === null) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes'))->with('error', 'Box surat penyerahan tidak ditemukan.');
        }

        $items = $this->suratPenyerahan
            ->where('box_id', $id)
            ->orderBy('no_surat', 'asc')
            ->findAll();

        return view('admin/surat_penyerahan_boxes/label', [
            'box' => $box,
            'items' => $items,
            'maxPerBox' => self::MAX_SURAT_PER_BOX,
        ]);
    }

    public function delete(int $id)
    {
        $box = $this->boxes->find($id);
        if ($box === null) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes'))->with('error', 'Box surat penyerahan tidak ditemukan.');
        }

        $count = $this->suratPenyerahan->where('box_id', $id)->countAllResults();
        if ($count > 0) {
            return redirect()->to(site_url('admin/surat-penyerahan-boxes'))->with('error', 'Box surat penyerahan tidak bisa dihapus karena masih berisi data.');
        }

        $this->boxes->delete($id);

        return redirect()->to(site_url('admin/surat-penyerahan-boxes'))->with('success', 'Box surat penyerahan berhasil dihapus.');
    }

    private function normalizeBoxCode(string $code): string
    {
        $code = strtoupper(trim($code));

        if ($code === '') {
            return $this->nextSequentialBoxCode();
        }

        if (strpos($code, 'SP-') === 0) {
            $suffix = substr($code, 3);
            if (ctype_digit($suffix)) {
                return 'SP-' . str_pad($suffix, 2, '0', STR_PAD_LEFT);
            }

            return $code;
        }

        if (ctype_digit($code)) {
            return 'SP-' . str_pad($code, 2, '0', STR_PAD_LEFT);
        }

        return 'SP-' . $code;
    }

    private function nextSequentialBoxCode(): string
    {
        $rows = $this->boxes
            ->select('box_code')
            ->like('box_code', 'SP-', 'after')
            ->findAll();

        $maxNumber = 0;
        foreach ($rows as $row) {
            $boxCode = (string) ($row['box_code'] ?? '');
            if (preg_match('/^SP-(\d+)$/', $boxCode, $matches) !== 1) {
                continue;
            }

            $number = (int) $matches[1];
            if ($number > $maxNumber) {
                $maxNumber = $number;
            }
        }

        return 'SP-' . str_pad((string) ($maxNumber + 1), 2, '0', STR_PAD_LEFT);
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

    private function countSuratByBox(int $boxId): int
    {
        return $this->suratPenyerahan->where('box_id', $boxId)->countAllResults();
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
            $targetCount = $this->countSuratByBox((int) $target['id']);
            if (($sourceCount + $targetCount) > self::MAX_SURAT_PER_BOX) {
                continue;
            }

            $result[] = $target + [
                'surat_count' => $targetCount,
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

                $parts[strtoupper($item)] = $item;
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

    private function findSuratItemsByLocationInBox(int $boxId, string $location): array
    {
        $items = $this->suratPenyerahan
            ->where('box_id', $boxId)
            ->orderBy('id', 'asc')
            ->findAll();

        $normalized = strtoupper(trim($location));
        return array_values(array_filter($items, static function (array $item) use ($normalized): bool {
            return strtoupper(trim((string) ($item['lokasi'] ?? ''))) === $normalized;
        }));
    }
}
