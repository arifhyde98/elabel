<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SuratPenyerahanBoxModel;
use App\Models\SuratPenyerahanModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SuratPenyerahanController extends BaseController
{
    private const MAX_SURAT_PER_BOX = 40;

    private SuratPenyerahanModel $suratPenyerahan;
    private SuratPenyerahanBoxModel $suratPenyerahanBoxes;

    public function __construct()
    {
        helper(['form']);
        $this->suratPenyerahan = new SuratPenyerahanModel();
        $this->suratPenyerahanBoxes = new SuratPenyerahanBoxModel();
    }

    public function index(): string
    {
        return view('admin/surat_penyerahan/index', [
            'items' => $this->suratPenyerahan->orderBy('id', 'desc')->findAll(),
            'activeMenu' => 'surat_penyerahan',
        ]);
    }

    public function create(): string
    {
        return view('admin/surat_penyerahan/create', [
            'activeMenu' => 'surat_penyerahan',
        ]);
    }

    public function store()
    {
        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = $this->suratPenyerahanPayload();
        $payload['box_id'] = $this->resolveSuratPenyerahanBoxId($payload['lokasi'] ?? null);
        $newId = $this->suratPenyerahan->insert($payload);
        $this->logActivity('create', 'Surat Penyerahan', 'Menambahkan surat penyerahan ' . ($payload['no_surat'] ?? '-') . '.', 'surat_penyerahan', (int) $newId);

        return redirect()->to(site_url('admin/surat-penyerahan'))->with('success', 'Data surat penyerahan berhasil ditambahkan.');
    }

    public function show(int $id)
    {
        $item = $this->suratPenyerahan->find($id);
        if (! $item) {
            return redirect()->to(site_url('admin/surat-penyerahan'))->with('error', 'Data surat penyerahan tidak ditemukan.');
        }

        return view('admin/surat_penyerahan/show', [
            'item' => $item,
            'activeMenu' => 'surat_penyerahan',
        ]);
    }

    public function edit(int $id)
    {
        $item = $this->suratPenyerahan->find($id);
        if (! $item) {
            return redirect()->to(site_url('admin/surat-penyerahan'))->with('error', 'Data surat penyerahan tidak ditemukan.');
        }

        return view('admin/surat_penyerahan/edit', [
            'item' => $item,
            'activeMenu' => 'surat_penyerahan',
        ]);
    }

    public function update(int $id)
    {
        $item = $this->suratPenyerahan->find($id);
        if (! $item) {
            return redirect()->to(site_url('admin/surat-penyerahan'))->with('error', 'Data surat penyerahan tidak ditemukan.');
        }

        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = $this->suratPenyerahanPayload();
        $payload['box_id'] = $this->resolveSuratPenyerahanBoxId($payload['lokasi'] ?? null, $id, isset($item['box_id']) ? (int) $item['box_id'] : null);
        $this->suratPenyerahan->update($id, $payload);
        $this->logActivity('update', 'Surat Penyerahan', 'Mengubah surat penyerahan ' . ($payload['no_surat'] ?? '-') . '.', 'surat_penyerahan', $id);

        return redirect()->to(site_url('admin/surat-penyerahan'))->with('success', 'Data surat penyerahan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $item = $this->suratPenyerahan->find($id);
        if (! $item) {
            return redirect()->to(site_url('admin/surat-penyerahan'))->with('error', 'Data surat penyerahan tidak ditemukan.');
        }

        $this->suratPenyerahan->delete($id);
        $this->logActivity('delete', 'Surat Penyerahan', 'Menghapus surat penyerahan ' . ($item['no_surat'] ?? '-') . '.', 'surat_penyerahan', $id);

        return redirect()->to(site_url('admin/surat-penyerahan'))->with('success', 'Data surat penyerahan berhasil dihapus.');
    }

    public function import()
    {
        $rules = [
            'import_file' => 'uploaded[import_file]|max_size[import_file,5120]|ext_in[import_file,xlsx,xls,csv]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('admin/surat-penyerahan'))
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('openModal', 'import');
        }

        $file = $this->request->getFile('import_file');
        if (! $file || ! $file->isValid()) {
            return redirect()->to(site_url('admin/surat-penyerahan'))
                ->with('error', 'File import tidak valid.')
                ->with('openModal', 'import');
        }

        $importPath = null;
        try {
            $importDir = WRITEPATH . 'uploads/imports';
            if (! is_dir($importDir)) {
                mkdir($importDir, 0755, true);
            }

            $extension = strtolower((string) $file->getClientExtension());
            if ($extension === '') {
                $extension = strtolower((string) $file->getExtension());
            }
            if ($extension === '') {
                $extension = 'xlsx';
            }

            $importPath = $importDir . DIRECTORY_SEPARATOR . uniqid('surat-penyerahan-import-', true) . '.' . $extension;
            $file->move($importDir, basename($importPath));

            $reader = IOFactory::createReaderForFile($importPath);
            if ($extension === 'csv' && method_exists($reader, 'setReadDataOnly')) {
                $reader->setReadDataOnly(true);
            }
            $rows = $reader->load($importPath)->getActiveSheet()->toArray('', true, true, true);
        } catch (\Throwable $exception) {
            if ($importPath && is_file($importPath)) {
                @unlink($importPath);
            }

            $message = 'File import tidak dapat dibaca. Gunakan format XLSX, XLS, atau CSV yang valid.';
            if (strtolower((string) ENVIRONMENT) === 'development') {
                $message .= ' Detail: ' . $exception->getMessage();
            }

            return redirect()->to(site_url('admin/surat-penyerahan'))
                ->with('error', $message)
                ->with('openModal', 'import');
        }

        if ($importPath && is_file($importPath)) {
            @unlink($importPath);
        }

        if (count($rows) < 2) {
            return redirect()->to(site_url('admin/surat-penyerahan'))
                ->with('error', 'File import tidak berisi data.')
                ->with('openModal', 'import');
        }

        $parsedRows = $this->extractImportRows($rows);
        $successCount = 0;
        $errorMessages = [];

        foreach ($parsedRows as $rowNumber => $row) {
            if ($this->isImportRowEmpty($row)) {
                continue;
            }

            if (($row['no_surat'] ?? '') === '') {
                $errorMessages[] = 'Baris ' . $rowNumber . ': No. Surat wajib diisi.';
                continue;
            }

            $payload = $this->normalizeImportPayload($row);
            $payload['box_id'] = $this->resolveSuratPenyerahanBoxId($payload['lokasi'] ?? null);
            $result = $this->suratPenyerahan->insert($payload);
            if ($result === false) {
                $errors = $this->suratPenyerahan->errors();
                $errorMessages[] = 'Baris ' . $rowNumber . ': ' . implode(', ', $errors ?: ['gagal disimpan']);
                continue;
            }

            $successCount++;
        }

        if ($successCount === 0 && $errorMessages !== []) {
            return redirect()->to(site_url('admin/surat-penyerahan'))
                ->with('error', implode(' ', array_slice($errorMessages, 0, 5)))
                ->with('openModal', 'import');
        }

        $message = $successCount . ' data surat penyerahan berhasil diimport.';
        if ($errorMessages !== []) {
            $message .= ' ' . min(count($errorMessages), 5) . ' baris dilewati: ' . implode(' ', array_slice($errorMessages, 0, 5));
        }
        $this->logActivity('import', 'Surat Penyerahan', 'Mengimport ' . $successCount . ' data surat penyerahan.', 'surat_penyerahan', null);

        return redirect()->to(site_url('admin/surat-penyerahan'))->with('success', $message);
    }

    public function export()
    {
        $items = $this->suratPenyerahan->orderBy('id', 'desc')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Surat Penyerahan');
        $sheet->fromArray([[
            'No',
            'NIBAR',
            'No. Surat',
            'Status Penggunaan',
            'Luas',
            'Tahun',
            'Lokasi',
            'Pemberi Hibah',
        ]], null, 'A1');

        $rowIndex = 2;
        $i = 1;
        foreach ($items as $item) {
            $sheet->fromArray([[
                $i++,
                $item['nibar'] ?? '',
                $item['no_surat'] ?? '',
                $item['status_penggunaan'] ?? '',
                $item['luas'] ?? '',
                $item['tahun'] ?? '',
                $item['lokasi'] ?? '',
                $item['pemberi_hibah'] ?? '',
            ]], null, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'surat-penyerahan-');
        if ($tempFile === false) {
            return $this->response;
        }

        $writer->save($tempFile);
        $contents = file_get_contents($tempFile);
        @unlink($tempFile);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="surat-penyerahan-' . date('Ymd') . '.xlsx"')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setBody($contents ?: '');
    }

    public function downloadImportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Import Surat Penyerahan');
        $sheet->fromArray([
            ['No', 'NIBAR', 'No. Surat', 'Status Penggunaan', 'Luas', 'Tahun', 'Lokasi', 'Pemberi Hibah'],
            [1, 'NBR-001', '593/001/BPKAD/2026', 'Dipakai', '250.50', '2026', 'Donggala', 'Nama Pemberi Hibah'],
        ], null, 'A1');

        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'surat-penyerahan-template-');
        if ($tempFile === false) {
            return $this->response;
        }

        $writer->save($tempFile);
        $contents = file_get_contents($tempFile);
        @unlink($tempFile);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="format-import-surat-penyerahan.xlsx"')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setBody($contents ?: '');
    }

    private function suratPenyerahanPayload(): array
    {
        return [
            'nibar'             => $this->nullIfEmpty((string) $this->request->getPost('nibar')),
            'no_surat'          => trim((string) $this->request->getPost('no_surat')),
            'status_penggunaan' => $this->nullIfEmpty((string) $this->request->getPost('status_penggunaan')),
            'luas'              => $this->decimalOrNull((string) $this->request->getPost('luas')),
            'tahun'             => $this->integerOrNull((string) $this->request->getPost('tahun')),
            'lokasi'            => $this->nullIfEmpty((string) $this->request->getPost('lokasi')),
            'pemberi_hibah'     => $this->nullIfEmpty((string) $this->request->getPost('pemberi_hibah')),
            'box_id'            => null,
        ];
    }

    private function validationRules(): array
    {
        return [
            'nibar'             => 'permit_empty|max_length[100]',
            'no_surat'          => 'required|max_length[150]',
            'status_penggunaan' => 'permit_empty|max_length[150]',
            'luas'              => 'permit_empty|decimal',
            'tahun'             => 'permit_empty|integer|greater_than_equal_to[1900]|less_than_equal_to[2100]',
            'lokasi'            => 'permit_empty|max_length[255]',
            'pemberi_hibah'     => 'permit_empty|max_length[150]',
            'box_id'            => 'permit_empty|integer',
        ];
    }

    private function nullIfEmpty(string $value): ?string
    {
        $value = trim($value);
        return $value === '' ? null : $value;
    }

    private function decimalOrNull(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $value = preg_replace('/[^0-9,.\-]/', '', $value) ?? '';
        if ($value === '' || $value === '-' || $value === '.' || $value === ',') {
            return null;
        }

        $lastComma = strrpos($value, ',');
        $lastDot = strrpos($value, '.');

        if ($lastComma !== false && $lastDot !== false) {
            if ($lastComma > $lastDot) {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                $value = str_replace(',', '', $value);
            }
        } elseif ($lastComma !== false) {
            $parts = explode(',', $value);
            $lastPart = end($parts);
            if ($lastPart !== false && strlen($lastPart) <= 2) {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                $value = str_replace(',', '', $value);
            }
        } elseif ($lastDot !== false) {
            $parts = explode('.', $value);
            $lastPart = end($parts);
            if ($lastPart !== false && strlen($lastPart) > 2) {
                $value = str_replace('.', '', $value);
            }
        }

        return $value === '' ? null : $value;
    }

    private function integerOrNull(string $value): ?int
    {
        $value = trim($value);
        return $value === '' ? null : (int) $value;
    }

    private function extractImportRows(array $rows): array
    {
        $headerRow = array_shift($rows) ?: [];
        $headerMap = $this->buildImportHeaderMap($headerRow);
        $parsedRows = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $parsedRows[$rowNumber] = [
                'nibar'             => trim((string) $this->importCellValue($row, $headerMap, 'nibar', 'B')),
                'no_surat'          => trim((string) $this->importCellValue($row, $headerMap, 'no_surat', 'C')),
                'status_penggunaan' => trim((string) $this->importCellValue($row, $headerMap, 'status_penggunaan', 'D')),
                'luas'              => trim((string) $this->importCellValue($row, $headerMap, 'luas', 'E')),
                'tahun'             => trim((string) $this->importCellValue($row, $headerMap, 'tahun', 'F')),
                'lokasi'            => trim((string) $this->importCellValue($row, $headerMap, 'lokasi', 'G')),
                'pemberi_hibah'     => trim((string) $this->importCellValue($row, $headerMap, 'pemberi_hibah', 'H')),
            ];
        }

        return $parsedRows;
    }

    private function buildImportHeaderMap(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $column => $header) {
            $normalizedHeader = strtolower(trim((string) $header));
            $normalizedHeader = preg_replace('/\s+/', ' ', $normalizedHeader ?? '');

            if ($normalizedHeader === 'nibar') {
                $map['nibar'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['no. surat', 'no surat', 'nomor surat'], true)) {
                $map['no_surat'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['status penggunaan', 'status_penggunaan'], true)) {
                $map['status_penggunaan'] = $column;
                continue;
            }
            if ($normalizedHeader === 'luas') {
                $map['luas'] = $column;
                continue;
            }
            if ($normalizedHeader === 'tahun') {
                $map['tahun'] = $column;
                continue;
            }
            if ($normalizedHeader === 'lokasi') {
                $map['lokasi'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['pemberi hibah', 'pemberi_hibah'], true)) {
                $map['pemberi_hibah'] = $column;
            }
        }

        return $map;
    }

    private function importCellValue(array $row, array $headerMap, string $field, string $fallbackColumn): string
    {
        $column = $headerMap[$field] ?? $fallbackColumn;
        return (string) ($row[$column] ?? '');
    }

    private function isImportRowEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeImportPayload(array $row): array
    {
        return [
            'nibar'             => $this->nullIfEmpty((string) ($row['nibar'] ?? '')),
            'no_surat'          => trim((string) ($row['no_surat'] ?? '')),
            'status_penggunaan' => $this->nullIfEmpty((string) ($row['status_penggunaan'] ?? '')),
            'luas'              => $this->decimalOrNull((string) ($row['luas'] ?? '')),
            'tahun'             => $this->integerOrNull((string) ($row['tahun'] ?? '')),
            'lokasi'            => $this->nullIfEmpty((string) ($row['lokasi'] ?? '')),
            'pemberi_hibah'     => $this->nullIfEmpty((string) ($row['pemberi_hibah'] ?? '')),
            'box_id'            => null,
        ];
    }

    private function resolveSuratPenyerahanBoxId(?string $lokasi, ?int $excludeSuratId = null, ?int $preferredBoxId = null): ?int
    {
        $lokasi = $this->nullIfEmpty((string) $lokasi);
        if ($lokasi === null) {
            return null;
        }

        $normalizedLokasi = trim($lokasi);
        $boxes = $this->findSuratPenyerahanBoxesByLocation($normalizedLokasi);

        if ($preferredBoxId !== null) {
            foreach ($boxes as $box) {
                if ((int) ($box['id'] ?? 0) !== $preferredBoxId) {
                    continue;
                }

                $count = $this->countSuratPenyerahanByBox($preferredBoxId, $excludeSuratId);
                if ($count < self::MAX_SURAT_PER_BOX) {
                    return $preferredBoxId;
                }
            }
        }

        if ($boxes !== []) {
            foreach ($boxes as $box) {
                $count = $this->countSuratPenyerahanByBox((int) $box['id'], $excludeSuratId);
                if ($count < self::MAX_SURAT_PER_BOX) {
                    return (int) $box['id'];
                }
            }
        }

        $newBoxId = $this->suratPenyerahanBoxes->insert([
            'box_code' => $boxes !== []
                ? $this->nextSuratPenyerahanBoxCodeSuffix((string) ($boxes[0]['box_code'] ?? ''))
                : $this->nextSuratPenyerahanBoxCode(),
            'lokasi' => $normalizedLokasi,
            'created_by' => session()->get('user_id') ? (int) session()->get('user_id') : null,
        ], true);

        return $newBoxId ? (int) $newBoxId : null;
    }

    private function countSuratPenyerahanByBox(int $boxId, ?int $excludeSuratId = null): int
    {
        $builder = $this->suratPenyerahan->where('box_id', $boxId);
        if ($excludeSuratId !== null) {
            $builder->where('id !=', $excludeSuratId);
        }

        return $builder->countAllResults();
    }

    private function nextSuratPenyerahanBoxCode(): string
    {
        $rows = $this->suratPenyerahanBoxes
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

    private function nextSuratPenyerahanBoxCodeSuffix(string $baseCode): string
    {
        $baseCode = preg_replace('/ \(\d+\)$/', '', trim($baseCode)) ?? trim($baseCode);
        $existing = $this->suratPenyerahanBoxes
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

    private function findSuratPenyerahanBoxesByLocation(string $lokasi): array
    {
        $normalized = strtoupper(trim($lokasi));
        if ($normalized === '') {
            return [];
        }

        return array_values(array_filter(
            $this->suratPenyerahanBoxes->orderBy('id', 'asc')->findAll(),
            static function (array $box) use ($normalized): bool {
                $boxLocations = array_map(
                    static fn (string $value): string => strtoupper(trim($value)),
                    preg_split('/\s*,\s*/', (string) ($box['lokasi'] ?? '')) ?: []
                );

                return in_array($normalized, array_filter($boxLocations), true);
            }
        ));
    }
}
