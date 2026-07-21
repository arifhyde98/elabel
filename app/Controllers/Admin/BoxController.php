<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BoxModel;
use App\Models\BoxYearModel;
use App\Models\BpkbModel;

class BoxController extends BaseController
{
    private const MAX_BPKB_MERGE_SOURCE = 25;

    private BoxModel $boxes;
    private BoxYearModel $boxYears;
    private BpkbModel $bpkb;

    public function __construct()
    {
        $this->boxes = new BoxModel();
        $this->boxYears = new BoxYearModel();
        $this->bpkb  = new BpkbModel();
    }

    public function index(?string $type = null): string
    {
        $vehicleType = $this->normalizeVehicleType($type);
        $vehicleLabel = $this->vehicleLabel($vehicleType);

        $builder = $this->boxes
            ->select('boxes.*, COUNT(bpkb.id) as bpkb_count')
            ->join('bpkb', "bpkb.box_id = boxes.id AND bpkb.status NOT IN ('Dihapus','Dipinjam')", 'left');
        if ($vehicleType !== null) {
            $builder->where('boxes.vehicle_type', $vehicleType);
        }
        $boxes = $builder
            ->groupBy('boxes.id')
            ->orderBy('boxes.id', 'desc')
            ->findAll();

        $yearsMap = [];
        if (! empty($boxes)) {
            $boxIds = array_column($boxes, 'id');
            $years  = $this->boxYears->whereIn('box_id', $boxIds)->orderBy('year', 'desc')->findAll();
            foreach ($years as $row) {
                $yearsMap[$row['box_id']][] = $row['year'];
            }
        }

        return view('admin/boxes/index', [
            'boxes'      => $boxes,
            'yearsMap'   => $yearsMap,
            'vehicleType' => $vehicleType,
            'vehicleLabel' => $vehicleLabel,
            'vehicleRoute' => $vehicleType ? $this->routeSegment($vehicleType) : null,
            'activeMenu' => $vehicleType === 'R2' ? 'boxes_motor' : ($vehicleType === 'R4' ? 'boxes_mobil' : 'boxes'),
        ]);
    }

    public function create(?string $type = null): string
    {
        $vehicleType = $this->normalizeVehicleType($type);
        $vehicleLabel = $this->vehicleLabel($vehicleType);
        $nextBoxCodes = [
            'R4' => $this->nextSequentialBoxCode('R4'),
            'R2' => $this->nextSequentialBoxCode('R2'),
        ];

        return view('admin/boxes/create', [
            'vehicleType' => $vehicleType,
            'vehicleLabel' => $vehicleLabel,
            'nextBoxCodes' => $nextBoxCodes,
            'vehicleRoute' => $vehicleType ? $this->routeSegment($vehicleType) : null,
            'activeMenu' => $vehicleType === 'R2' ? 'boxes_motor' : ($vehicleType === 'R4' ? 'boxes_mobil' : 'boxes'),
        ]);
    }

    public function store()
    {
        $rules = [
            'box_code' => 'required|max_length[30]',
            'location' => 'permit_empty|max_length[100]',
            'vehicle_type' => 'required|in_list[R4,R2]',
            'years'    => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $vehicleType = $this->normalizeVehicleType((string) $this->request->getPost('vehicle_type')) ?? 'R4';
        $boxCodeInput = (string) $this->request->getPost('box_code');
        $boxCode = $this->applyBoxPrefix($boxCodeInput, $vehicleType);

        if ($this->boxes->where('box_code', $boxCode)->first()) {
            return redirect()->back()->withInput()->with('error', 'Kode box sudah digunakan.');
        }
        $boxId = $this->boxes->insert([
            'box_code'   => $boxCode,
            'location'   => (string) $this->request->getPost('location') ?: null,
            'vehicle_type' => $vehicleType,
            'created_by' => (int) session()->get('user_id'),
        ], true);

        $yearsInput = (string) $this->request->getPost('years');
        $parts = preg_split('/\s*-\s*/', trim($yearsInput)) ?: [];
        foreach ($parts as $part) {
            $year = (int) $part;
            if ($year > 0) {
                $this->boxYears->insert([
                    'box_id' => (int) $boxId,
                    'year'   => $year,
                ]);
            }
        }
        $this->logActivity('create', 'Box BPKB', 'Menambahkan box ' . $boxCode . '.', 'boxes', (int) $boxId);

        return redirect()->to(site_url('admin/boxes/' . $this->routeSegment($vehicleType)))->with('success', 'Box berhasil ditambahkan.');
    }

    public function show(int $id): string
    {
        $box = $this->boxes->find($id);
        if (! $box) {
            return redirect()->to(site_url('admin/boxes'))->with('error', 'Box tidak ditemukan.');
        }

        $items = $this->bpkb
            ->where('box_id', $id)
            ->where('status !=', 'Dihapus')
            ->where('status !=', 'Dipinjam')
            ->orderBy('year', 'desc')
            ->orderBy('plate_number', 'asc')
            ->findAll();

        $years = $this->boxYears->where('box_id', $id)->orderBy('year', 'desc')->findAll();
        $mergeCandidateCount = $this->countActiveBpkbByBox($id);
        $mergeTargets = [];
        if ($mergeCandidateCount > 0 && $mergeCandidateCount <= self::MAX_BPKB_MERGE_SOURCE) {
            $mergeTargets = $this->availableMergeTargets($box, $id, $mergeCandidateCount);
        }

        return view('admin/boxes/show', [
            'box'        => $box,
            'items'      => $items,
            'years'      => $years,
            'mergeTargets' => $mergeTargets,
            'mergeCandidateCount' => $mergeCandidateCount,
            'maxMergeSource' => self::MAX_BPKB_MERGE_SOURCE,
            'activeMenu' => ($box['vehicle_type'] ?? '') === 'R2' ? 'boxes_motor' : 'boxes_mobil',
        ]);
    }

    public function merge(int $id)
    {
        $sourceBox = $this->boxes->find($id);
        if (! $sourceBox) {
            return redirect()->to(site_url('admin/boxes'))->with('error', 'Box sumber tidak ditemukan.');
        }

        $targetId = (int) $this->request->getPost('target_box_id');
        $targetBox = $this->boxes->find($targetId);
        if (! $targetBox) {
            return redirect()->to(site_url('admin/boxes/' . $id))->with('error', 'Box tujuan tidak ditemukan.');
        }

        if ($targetId === $id) {
            return redirect()->to(site_url('admin/boxes/' . $id))->with('error', 'Box tujuan tidak boleh sama dengan box sumber.');
        }

        if (($sourceBox['vehicle_type'] ?? null) !== ($targetBox['vehicle_type'] ?? null)) {
            return redirect()->to(site_url('admin/boxes/' . $id))->with('error', 'Penggabungan hanya bisa dilakukan ke box dengan jenis kendaraan yang sama.');
        }

        if (! $this->canMergeIntoTarget($id, $targetId)) {
            return redirect()->to(site_url('admin/boxes/' . $id))->with('error', 'Penggabungan hanya bisa dilakukan jika tahun box sumber sudah tercakup di box tujuan.');
        }

        $sourceCount = $this->countActiveBpkbByBox($id);
        if ($sourceCount === 0) {
            return redirect()->to(site_url('admin/boxes/' . $id))->with('error', 'Box sumber tidak memiliki data BPKB untuk digabung.');
        }

        if ($sourceCount > self::MAX_BPKB_MERGE_SOURCE) {
            return redirect()->to(site_url('admin/boxes/' . $id))->with('error', 'Box hanya bisa digabung jika berisi maksimal ' . self::MAX_BPKB_MERGE_SOURCE . ' BPKB.');
        }

        $bpkbItems = $this->bpkb
            ->where('box_id', $id)
            ->where('status !=', 'Dihapus')
            ->findAll();

        $this->bpkb->db->transStart();

        foreach ($bpkbItems as $item) {
            $this->bpkb->update((int) $item['id'], [
                'box_id' => $targetId,
            ]);
        }

        $this->mergeBoxYears($id, $targetId);
        $this->boxYears->where('box_id', $id)->delete();
        $this->boxes->delete($id);

        $this->bpkb->db->transComplete();

        if (! $this->bpkb->db->transStatus()) {
            return redirect()->to(site_url('admin/boxes/' . $id))->with('error', 'Penggabungan box gagal disimpan.');
        }
        $this->logActivity('delete', 'Box BPKB', 'Menggabungkan box ' . $sourceBox['box_code'] . ' ke box ' . $targetBox['box_code'] . '.', 'boxes', $targetId);

        return redirect()->to(site_url('admin/boxes/' . $targetId))->with('success', 'Box ' . $sourceBox['box_code'] . ' berhasil digabung ke box ' . $targetBox['box_code'] . '.');
    }

    public function label(int $id): string
    {
        $box = $this->boxes->find($id);
        if (! $box) {
            return redirect()->to(site_url('admin/boxes'))->with('error', 'Box tidak ditemukan.');
        }

        $items = $this->bpkb
            ->where('box_id', $id)
            ->where('status !=', 'Dihapus')
            ->orderBy('year', 'desc')
            ->orderBy('plate_number', 'asc')
            ->findAll();

        $boxYears = $this->boxYears
            ->where('box_id', $id)
            ->orderBy('year', 'desc')
            ->findAll();

        return view('admin/boxes/label', [
            'box'      => $box,
            'items'    => $items,
            'boxYears' => $boxYears,
        ]);
    }

    public function delete(int $id)
    {
        $box = $this->boxes->find($id);
        if (! $box) {
            return redirect()->to(site_url('admin/boxes'))->with('error', 'Box tidak ditemukan.');
        }

        $count = $this->bpkb->where('box_id', $id)->countAllResults();
        if ($count > 0) {
            return redirect()->to(site_url('admin/boxes'))->with('error', 'Box tidak bisa dihapus karena masih berisi data BPKB.');
        }

        $this->boxYears->where('box_id', $id)->delete();
        $this->boxes->delete($id);
        $this->logActivity('delete', 'Box BPKB', 'Menghapus box ' . ($box['box_code'] ?? '-') . '.', 'boxes', $id);

        return redirect()->to(site_url('admin/boxes'))->with('success', 'Box berhasil dihapus.');
    }

    private function normalizeVehicleType(?string $type): ?string
    {
        if ($type === null) {
            return null;
        }

        $type = strtolower(trim($type));
        if (! in_array($type, ['mobil', 'motor', 'r4', 'r2'], true)) {
            return null;
        }

        if (in_array($type, ['motor', 'r2'], true)) {
            return 'R2';
        }
        if (in_array($type, ['mobil', 'r4'], true)) {
            return 'R4';
        }

        return strtoupper($type);
    }

    private function vehicleLabel(?string $type): string
    {
        if ($type === 'R2') {
            return 'R2';
        }
        if ($type === 'R4') {
            return 'R4';
        }

        return 'Semua';
    }

    private function routeSegment(?string $type): string
    {
        return $type === 'R2' ? 'r2' : 'r4';
    }

    private function applyBoxPrefix(string $code, string $vehicleType): string
    {
        $code = strtoupper(trim($code));
        $prefix = $vehicleType . '-';

        if ($code === '') {
            return $this->nextSequentialBoxCode($vehicleType);
        }

        if (strpos($code, $vehicleType . '-') === 0) {
            $suffix = substr($code, strlen($vehicleType) + 1);

            if (ctype_digit($suffix)) {
                return $prefix . str_pad($suffix, 2, '0', STR_PAD_LEFT);
            }

            return $code;
        }

        if (ctype_digit($code)) {
            return $prefix . str_pad($code, 2, '0', STR_PAD_LEFT);
        }

        return $prefix . $code;
    }

    private function nextSequentialBoxCode(string $vehicleType): string
    {
        $boxes = $this->boxes
            ->select('box_code')
            ->where('vehicle_type', $vehicleType)
            ->like('box_code', $vehicleType . '-', 'after')
            ->findAll();

        $maxNumber = 0;
        foreach ($boxes as $box) {
            $boxCode = (string) ($box['box_code'] ?? '');
            if (preg_match('/^' . preg_quote($vehicleType, '/') . '-(\d+)$/', $boxCode, $matches) !== 1) {
                continue;
            }

            $number = (int) $matches[1];
            if ($number > $maxNumber) {
                $maxNumber = $number;
            }
        }

        return $vehicleType . '-' . str_pad((string) ($maxNumber + 1), 2, '0', STR_PAD_LEFT);
    }

    private function countActiveBpkbByBox(int $boxId): int
    {
        return $this->bpkb
            ->where('box_id', $boxId)
            ->where('status !=', 'Dihapus')
            ->countAllResults();
    }

    private function availableMergeTargets(array $sourceBox, int $sourceId, int $sourceCount): array
    {
        $targets = $this->boxes
            ->where('vehicle_type', $sourceBox['vehicle_type'] ?? null)
            ->where('id !=', $sourceId)
            ->orderBy('box_code', 'asc')
            ->findAll();

        if ($targets === []) {
            return [];
        }

        $targetIds = array_map(static fn (array $row): int => (int) $row['id'], $targets);
        $counts = [];
        if ($targetIds !== []) {
            $rows = $this->bpkb
                ->select('box_id, COUNT(id) AS total')
                ->whereIn('box_id', $targetIds)
                ->where('status !=', 'Dihapus')
                ->groupBy('box_id')
                ->findAll();

            foreach ($rows as $row) {
                $counts[(int) $row['box_id']] = (int) ($row['total'] ?? 0);
            }
        }

        $result = [];
        foreach ($targets as $target) {
            $targetCount = $counts[(int) $target['id']] ?? 0;
            if (! $this->canMergeIntoTarget($sourceId, (int) $target['id'])) {
                continue;
            }

            $result[] = $target + [
                'bpkb_count' => $targetCount,
            ];
        }

        return $result;
    }

    private function canMergeIntoTarget(int $sourceId, int $targetId): bool
    {
        $sourceYears = $this->boxYearValues($sourceId);
        $targetYears = $this->boxYearValues($targetId);

        if ($sourceYears === [] || $targetYears === []) {
            return false;
        }

        foreach ($sourceYears as $year) {
            if (! in_array($year, $targetYears, true)) {
                return false;
            }
        }

        return true;
    }

    private function boxYearValues(int $boxId): array
    {
        $years = $this->boxYears
            ->where('box_id', $boxId)
            ->orderBy('year', 'asc')
            ->findAll();

        return array_values(array_filter(
            array_map(static fn (array $row): int => (int) ($row['year'] ?? 0), $years),
            static fn (int $year): bool => $year > 0
        ));
    }

    private function mergeBoxYears(int $sourceId, int $targetId): void
    {
        $sourceYears = $this->boxYears->where('box_id', $sourceId)->findAll();
        if ($sourceYears === []) {
            return;
        }

        $targetYears = $this->boxYears->where('box_id', $targetId)->findAll();
        $targetMap = [];
        foreach ($targetYears as $row) {
            $targetMap[(int) $row['year']] = true;
        }

        foreach ($sourceYears as $row) {
            $year = (int) ($row['year'] ?? 0);
            if ($year <= 0 || isset($targetMap[$year])) {
                continue;
            }

            $this->boxYears->insert([
                'box_id' => $targetId,
                'year' => $year,
            ]);
            $targetMap[$year] = true;
        }
    }
}
