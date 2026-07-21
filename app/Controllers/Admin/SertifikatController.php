<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SertifikatBoxModel;
use App\Models\SertifikatModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SertifikatController extends BaseController
{
    private const MAX_SERTIFIKAT_PER_BOX = 40;

    private SertifikatModel $sertifikat;
    private SertifikatBoxModel $sertifikatBoxes;

    public function __construct()
    {
        helper(['form']);
        $this->sertifikat = new SertifikatModel();
        $this->sertifikatBoxes = new SertifikatBoxModel();
    }

    public function index(): string
    {
        $query = trim((string) $this->request->getGet('q'));
        $normalizedQuery = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $query) ?? '');

        $builder = $this->sertifikat->orderBy('id', 'desc');
        if ($query !== '') {
            $builder->groupStart()
                ->like('no_sertipikat', $query)
                ->orLike('nibar', $query)
                ->orLike('nama_pemilik', $query)
                ->orLike('lokasi', $query)
                ->orLike('dinas', $query);

            if ($normalizedQuery !== '') {
                $escapedQuery = db_connect()->escapeLikeString($normalizedQuery);
                $builder->orWhere(
                    "REPLACE(REPLACE(REPLACE(REPLACE(UPPER(nibar), ' ', ''), '.', ''), '-', ''), '/', '') LIKE '%{$escapedQuery}%'",
                    null,
                    false
                );
            }

            $builder->groupEnd();
        }

        return view('admin/sertifikat/index', [
            'items' => $builder->findAll(),
            'activeMenu' => 'sertifikat',
            'searchQuery' => $query,
        ]);
    }

    public function create(): string
    {
        return view('admin/sertifikat/create', [
            'activeMenu' => 'sertifikat',
        ]);
    }

    public function store()
    {
        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = $this->sertifikatPayload();
        $identity = $this->normalizeSertifikatIdentity($payload);
        $duplicate = $this->findDuplicateSertifikat($identity);
        if ($duplicate !== null) {
            return redirect()->back()->withInput()->with('error', $this->duplicateSertifikatMessage($duplicate));
        }

        $payload['box_id'] = $this->resolveSertifikatBoxId($payload['lokasi'] ?? null);
        $payload['pdf_path'] = $this->storeUploadedPdf($this->request->getFile('pdf'));
        $newId = $this->sertifikat->insert($payload);
        $this->logActivity('create', 'Sertipikat Tanah', 'Menambahkan sertipikat ' . ($payload['no_sertipikat'] ?? '-') . '.', 'sertifikat_tanah', (int) $newId);

        return redirect()->to(site_url('admin/sertifikat'))->with('success', 'Data sertipikat berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        $item = $this->sertifikat->find($id);
        if (! $item) {
            return redirect()->to(site_url('admin/sertifikat'))->with('error', 'Data sertipikat tidak ditemukan.');
        }

        return view('admin/sertifikat/edit', [
            'item' => $item,
            'activeMenu' => 'sertifikat',
        ]);
    }

    public function show(int $id): string
    {
        $item = $this->sertifikat->find($id);
        if (! $item) {
            return redirect()->to(site_url('admin/sertifikat'))->with('error', 'Data sertipikat tidak ditemukan.');
        }

        return view('admin/sertifikat/show', [
            'item' => $item,
            'activeMenu' => 'sertifikat',
        ]);
    }

    public function update(int $id)
    {
        $item = $this->sertifikat->find($id);
        if (! $item) {
            return redirect()->to(site_url('admin/sertifikat'))->with('error', 'Data sertipikat tidak ditemukan.');
        }

        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = $this->sertifikatPayload();
        $identity = $this->normalizeSertifikatIdentity($payload);
        $duplicate = $this->findDuplicateSertifikat($identity, $id);
        if ($duplicate !== null) {
            return redirect()->back()->withInput()->with('error', $this->duplicateSertifikatMessage($duplicate));
        }

        $payload['box_id'] = $this->resolveSertifikatBoxId($payload['lokasi'] ?? null, $id, isset($item['box_id']) ? (int) $item['box_id'] : null);
        $payload['pdf_path'] = $this->replaceUploadedPdf($this->request->getFile('pdf'), (string) ($item['pdf_path'] ?? ''));
        $this->sertifikat->update($id, $payload);
        $this->logActivity('update', 'Sertipikat Tanah', 'Mengubah sertipikat ' . ($payload['no_sertipikat'] ?? '-') . '.', 'sertifikat_tanah', $id);

        return redirect()->to(site_url('admin/sertifikat'))->with('success', 'Data sertipikat berhasil diperbarui.');
    }

    public function viewPdf(int $id)
    {
        $item = $this->sertifikat->find($id);
        if (! $item || empty($item['pdf_path'])) {
            return redirect()->to(site_url('admin/sertifikat'))->with('error', 'File PDF sertipikat tidak ditemukan.');
        }

        $path = WRITEPATH . $item['pdf_path'];
        if (! is_file($path)) {
            return redirect()->to(site_url('admin/sertifikat'))->with('error', 'File PDF sertipikat tidak ditemukan.');
        }

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="sertipikat-' . $id . '.pdf"')
            ->setBody(file_get_contents($path));
    }

    public function import()
    {
        $rules = [
            'import_file' => 'uploaded[import_file]|max_size[import_file,5120]|ext_in[import_file,xlsx,xls,csv]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('admin/sertifikat'))
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('openModal', 'import');
        }

        $file = $this->request->getFile('import_file');
        if (! $file || ! $file->isValid()) {
            return redirect()->to(site_url('admin/sertifikat'))
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

            $importPath = $importDir . DIRECTORY_SEPARATOR . uniqid('sertifikat-import-', true) . '.' . $extension;
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

            return redirect()->to(site_url('admin/sertifikat'))
                ->with('error', $message)
                ->with('openModal', 'import');
        }

        if ($importPath && is_file($importPath)) {
            @unlink($importPath);
        }

        if (count($rows) < 2) {
            return redirect()->to(site_url('admin/sertifikat'))
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

            if (($row['no_sertipikat'] ?? '') === '') {
                $errorMessages[] = 'Baris ' . $rowNumber . ': No. Sertipikat wajib diisi.';
                continue;
            }

            $payload = $this->normalizeImportPayload($row);
            $duplicate = $this->findDuplicateSertifikat($this->normalizeSertifikatIdentity($payload));
            if ($duplicate !== null) {
                $errorMessages[] = 'Baris ' . $rowNumber . ': ' . $this->duplicateSertifikatMessage($duplicate);
                continue;
            }

            $payload['box_id'] = $this->resolveSertifikatBoxId($payload['lokasi'] ?? null);
            $result = $this->sertifikat->insert($payload);
            if ($result === false) {
                $errors = $this->sertifikat->errors();
                $errorMessages[] = 'Baris ' . $rowNumber . ': ' . implode(', ', $errors ?: ['gagal disimpan']);
                continue;
            }

            $successCount++;
        }

        if ($successCount === 0 && $errorMessages !== []) {
            return redirect()->to(site_url('admin/sertifikat'))
                ->with('error', implode(' ', array_slice($errorMessages, 0, 5)))
                ->with('openModal', 'import');
        }

        $message = $successCount . ' data sertipikat berhasil diimport.';
        if ($errorMessages !== []) {
            $message .= ' ' . min(count($errorMessages), 5) . ' baris dilewati: ' . implode(' ', array_slice($errorMessages, 0, 5));
        }
        $this->logActivity('import', 'Sertipikat Tanah', 'Mengimport ' . $successCount . ' data sertipikat.', 'sertifikat_tanah', null);

        return redirect()->to(site_url('admin/sertifikat'))->with('success', $message);
    }

    public function export()
    {
        $items = $this->sertifikat->orderBy('id', 'desc')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[
            'No',
            'No. Sertipikat',
            'NIBAR',
            'Status Penggunaan',
            'Spesifikasi',
            'Luas',
            'Tanggal Perolehan',
            'Nilai Perolehan',
            'Nama Pemilik',
            'Cara Perolehan',
            'Alamat',
            'Lokasi',
            'Dinas',
        ]], null, 'A1');

        $rowIndex = 2;
        $i = 1;
        foreach ($items as $item) {
            $sheet->fromArray([[
                $i++,
                $item['no_sertipikat'] ?? '',
                $item['nibar'] ?? '',
                $item['status_penggunaan'] ?? '',
                $item['spesifikasi'] ?? '',
                $item['luas'] ?? '',
                $item['tanggal_perolehan'] ?? '',
                $item['nilai_perolehan'] ?? '',
                $item['nama_pemilik'] ?? '',
                $item['cara_perolehan'] ?? '',
                $item['alamat'] ?? '',
                $item['lokasi'] ?? '',
                $item['dinas'] ?? '',
            ]], null, 'A' . $rowIndex);
            $rowIndex++;
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'sertifikat-');
        if ($tempFile === false) {
            return $this->response;
        }

        $writer->save($tempFile);
        $contents = file_get_contents($tempFile);
        @unlink($tempFile);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="sertifikat-tanah-' . date('Ymd') . '.xlsx"')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setBody($contents ?: '');
    }

    public function downloadImportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Import Sertipikat');
        $sheet->fromArray([
            ['No', 'No. Sertipikat', 'NIBAR', 'Status Penggunaan', 'Spesifikasi', 'Luas', 'Tanggal Perolehan', 'Nilai Perolehan', 'Nama Pemilik', 'Cara Perolehan', 'Alamat', 'Lokasi', 'Dinas'],
            [1, '123/ABC/2024', 'NBR-001', 'Dipakai', 'Hak Pakai', '250.50', '2024-01-15', '150000000', 'Pemerintah Kabupaten Donggala', 'Pembelian', 'Jl. Contoh No.1', 'Donggala', 'BPKAD'],
        ], null, 'A1');

        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'sertifikat-template-');
        if ($tempFile === false) {
            return $this->response;
        }

        $writer->save($tempFile);
        $contents = file_get_contents($tempFile);
        @unlink($tempFile);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="format-import-sertifikat-tanah.xlsx"')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setBody($contents ?: '');
    }

    public function delete(int $id)
    {
        $item = $this->sertifikat->find($id);
        if (! $item) {
            return redirect()->to(site_url('admin/sertifikat'))->with('error', 'Data sertipikat tidak ditemukan.');
        }

        if (! empty($item['pdf_path'])) {
            $absolutePath = WRITEPATH . $item['pdf_path'];
            if (is_file($absolutePath)) {
                @unlink($absolutePath);
            }
        }

        $this->sertifikat->delete($id);
        $this->logActivity('delete', 'Sertipikat Tanah', 'Menghapus sertipikat ' . ($item['no_sertipikat'] ?? '-') . '.', 'sertifikat_tanah', $id);

        return redirect()->to(site_url('admin/sertifikat'))->with('success', 'Data sertipikat berhasil dihapus.');
    }

    private function sertifikatPayload(): array
    {
        return [
            'no_sertipikat'     => trim((string) $this->request->getPost('no_sertipikat')),
            'nibar'             => $this->nullIfEmpty((string) $this->request->getPost('nibar')),
            'status_penggunaan' => $this->nullIfEmpty((string) $this->request->getPost('status_penggunaan')),
            'spesifikasi'       => $this->nullIfEmpty((string) $this->request->getPost('spesifikasi')),
            'luas'              => $this->decimalOrNull((string) $this->request->getPost('luas')),
            'tanggal_perolehan' => $this->dateOrNull((string) $this->request->getPost('tanggal_perolehan')),
            'nilai_perolehan'   => $this->decimalOrNull((string) $this->request->getPost('nilai_perolehan')),
            'nama_pemilik'      => $this->nullIfEmpty((string) $this->request->getPost('nama_pemilik')),
            'cara_perolehan'    => $this->nullIfEmpty((string) $this->request->getPost('cara_perolehan')),
            'alamat'            => $this->nullIfEmpty((string) $this->request->getPost('alamat')),
            'lokasi'            => $this->nullIfEmpty((string) $this->request->getPost('lokasi')),
            'dinas'             => $this->nullIfEmpty((string) $this->request->getPost('dinas')),
            'box_id'            => null,
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

    private function dateOrNull(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $formats = [
            'Y-m-d',
            'd/m/Y',
            'm/d/Y',
            'd-m-Y',
            'm-d-Y',
            'd.m.Y',
            'm.d.Y',
            'j/n/Y',
            'n/j/Y',
            'j-n-Y',
            'n-j-Y',
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date instanceof \DateTime) {
                $errors = \DateTime::getLastErrors();
                if (($errors['warning_count'] ?? 0) === 0 && ($errors['error_count'] ?? 0) === 0) {
                    return $date->format('Y-m-d');
                }
            }
        }

        $timestamp = strtotime($value);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return $value;
    }

    private function validationRules(): array
    {
        return [
            'no_sertipikat'     => 'required|max_length[100]',
            'nibar'             => 'permit_empty|max_length[100]',
            'status_penggunaan' => 'permit_empty|max_length[100]',
            'spesifikasi'       => 'permit_empty|max_length[255]',
            'luas'              => 'permit_empty|decimal',
            'tanggal_perolehan' => 'permit_empty|valid_date',
            'nilai_perolehan'   => 'permit_empty|decimal',
            'nama_pemilik'      => 'permit_empty|max_length[150]',
            'cara_perolehan'    => 'permit_empty|max_length[150]',
            'alamat'            => 'permit_empty|max_length[255]',
            'lokasi'            => 'permit_empty|max_length[255]',
            'dinas'             => 'permit_empty|max_length[150]',
            'pdf'               => 'if_exist|max_size[pdf,5120]|ext_in[pdf,pdf]',
        ];
    }

    private function storeUploadedPdf($file): ?string
    {
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $uploadPath = WRITEPATH . 'uploads/sertifikat';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        return 'uploads/sertifikat/' . $newName;
    }

    private function replaceUploadedPdf($file, string $oldPath): ?string
    {
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $oldPath !== '' ? $oldPath : null;
        }

        $newPath = $this->storeUploadedPdf($file);
        if ($oldPath !== '' && $newPath !== null) {
            $absoluteOldPath = WRITEPATH . $oldPath;
            if (is_file($absoluteOldPath)) {
                @unlink($absoluteOldPath);
            }
        }

        return $newPath;
    }

    private function normalizeSertifikatIdentity(array $data): array
    {
        $normalize = static function ($value): ?string {
            $value = strtoupper(trim((string) $value));
            return $value === '' ? null : $value;
        };

        return [
            'no_sertipikat' => $normalize($data['no_sertipikat'] ?? null),
            'nibar'         => $normalize($data['nibar'] ?? null),
        ];
    }

    private function findDuplicateSertifikat(array $identity, ?int $excludeId = null): ?array
    {
        $fields = [
            'no_sertipikat' => 'No. Sertipikat',
            'nibar'         => 'NIBAR',
        ];

        foreach ($fields as $field => $label) {
            $value = $identity[$field] ?? null;
            if ($value === null) {
                continue;
            }

            $builder = $this->sertifikat->where($field, $value);
            if ($excludeId !== null) {
                $builder->where('id !=', $excludeId);
            }

            $duplicate = $builder->first();
            if ($duplicate !== null) {
                return [
                    'field' => $field,
                    'label' => $label,
                    'value' => $value,
                ];
            }
        }

        return null;
    }

    private function duplicateSertifikatMessage(array $duplicate): string
    {
        return $duplicate['label'] . ' "' . $duplicate['value'] . '" sudah terdaftar pada data sertipikat.';
    }

    private function extractImportRows(array $rows): array
    {
        $headerRow = array_shift($rows) ?: [];
        $headerMap = $this->buildImportHeaderMap($headerRow);
        $parsedRows = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $parsedRows[$rowNumber] = [
                'no_sertipikat'     => trim((string) $this->importCellValue($row, $headerMap, 'no_sertipikat', 'B')),
                'nibar'             => trim((string) $this->importCellValue($row, $headerMap, 'nibar', 'C')),
                'status_penggunaan' => trim((string) $this->importCellValue($row, $headerMap, 'status_penggunaan', 'D')),
                'spesifikasi'       => trim((string) $this->importCellValue($row, $headerMap, 'spesifikasi', 'E')),
                'luas'              => trim((string) $this->importCellValue($row, $headerMap, 'luas', 'F')),
                'tanggal_perolehan' => trim((string) $this->importCellValue($row, $headerMap, 'tanggal_perolehan', 'G')),
                'nilai_perolehan'   => trim((string) $this->importCellValue($row, $headerMap, 'nilai_perolehan', 'H')),
                'nama_pemilik'      => trim((string) $this->importCellValue($row, $headerMap, 'nama_pemilik', 'I')),
                'cara_perolehan'    => trim((string) $this->importCellValue($row, $headerMap, 'cara_perolehan', 'J')),
                'alamat'            => trim((string) $this->importCellValue($row, $headerMap, 'alamat', 'K')),
                'lokasi'            => trim((string) $this->importCellValue($row, $headerMap, 'lokasi', 'L')),
                'dinas'             => trim((string) $this->importCellValue($row, $headerMap, 'dinas', 'M')),
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

            if (in_array($normalizedHeader, ['no. sertipikat', 'no sertipikat', 'nomor sertipikat'], true)) {
                $map['no_sertipikat'] = $column;
                continue;
            }
            if ($normalizedHeader === 'nibar') {
                $map['nibar'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['status penggunaan', 'status_penggunaan'], true)) {
                $map['status_penggunaan'] = $column;
                continue;
            }
            if ($normalizedHeader === 'spesifikasi') {
                $map['spesifikasi'] = $column;
                continue;
            }
            if ($normalizedHeader === 'luas') {
                $map['luas'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['tanggal perolehan', 'tanggal_perolehan'], true)) {
                $map['tanggal_perolehan'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['nilai perolehan', 'nilai_perolehan'], true)) {
                $map['nilai_perolehan'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['nama pemilik', 'nama_pemilik'], true)) {
                $map['nama_pemilik'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['cara perolehan', 'cara_perolehan'], true)) {
                $map['cara_perolehan'] = $column;
                continue;
            }
            if ($normalizedHeader === 'alamat') {
                $map['alamat'] = $column;
                continue;
            }
            if ($normalizedHeader === 'lokasi') {
                $map['lokasi'] = $column;
                continue;
            }
            if ($normalizedHeader === 'dinas') {
                $map['dinas'] = $column;
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
            'no_sertipikat'     => trim((string) ($row['no_sertipikat'] ?? '')),
            'nibar'             => $this->nullIfEmpty((string) ($row['nibar'] ?? '')),
            'status_penggunaan' => $this->nullIfEmpty((string) ($row['status_penggunaan'] ?? '')),
            'spesifikasi'       => $this->nullIfEmpty((string) ($row['spesifikasi'] ?? '')),
            'luas'              => $this->decimalOrNull((string) ($row['luas'] ?? '')),
            'tanggal_perolehan' => $this->dateOrNull((string) ($row['tanggal_perolehan'] ?? '')),
            'nilai_perolehan'   => $this->decimalOrNull((string) ($row['nilai_perolehan'] ?? '')),
            'nama_pemilik'      => $this->nullIfEmpty((string) ($row['nama_pemilik'] ?? '')),
            'cara_perolehan'    => $this->nullIfEmpty((string) ($row['cara_perolehan'] ?? '')),
            'alamat'            => $this->nullIfEmpty((string) ($row['alamat'] ?? '')),
            'lokasi'            => $this->nullIfEmpty((string) ($row['lokasi'] ?? '')),
            'dinas'             => $this->nullIfEmpty((string) ($row['dinas'] ?? '')),
            'box_id'            => null,
        ];
    }

    private function resolveSertifikatBoxId(?string $lokasi, ?int $excludeSertifikatId = null, ?int $preferredBoxId = null): ?int
    {
        $lokasi = $this->nullIfEmpty((string) $lokasi);
        if ($lokasi === null) {
            return null;
        }

        $normalizedLokasi = trim($lokasi);
        $boxes = $this->findSertifikatBoxesByLocation($normalizedLokasi);

        if ($preferredBoxId !== null) {
            foreach ($boxes as $box) {
                if ((int) ($box['id'] ?? 0) !== $preferredBoxId) {
                    continue;
                }

                $count = $this->countSertifikatByBox($preferredBoxId, $excludeSertifikatId);
                if ($count < self::MAX_SERTIFIKAT_PER_BOX) {
                    return $preferredBoxId;
                }
            }
        }

        if ($boxes !== []) {
            foreach ($boxes as $box) {
                $count = $this->countSertifikatByBox((int) $box['id'], $excludeSertifikatId);
                if ($count < self::MAX_SERTIFIKAT_PER_BOX) {
                    return (int) $box['id'];
                }
            }
        }

        $newBoxId = $this->sertifikatBoxes->insert([
            'box_code' => $boxes !== []
                ? $this->nextSertifikatBoxCodeSuffix((string) ($boxes[0]['box_code'] ?? ''))
                : $this->nextSertifikatBoxCode(),
            'lokasi' => $normalizedLokasi,
            'created_by' => session()->get('user_id') ? (int) session()->get('user_id') : null,
        ], true);

        return $newBoxId ? (int) $newBoxId : null;
    }

    private function countSertifikatByBox(int $boxId, ?int $excludeSertifikatId = null): int
    {
        $builder = $this->sertifikat->where('box_id', $boxId);
        if ($excludeSertifikatId !== null) {
            $builder->where('id !=', $excludeSertifikatId);
        }

        return $builder->countAllResults();
    }

    private function nextSertifikatBoxCode(): string
    {
        $rows = $this->sertifikatBoxes
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

    private function nextSertifikatBoxCodeSuffix(string $baseCode): string
    {
        $baseCode = preg_replace('/ \(\d+\)$/', '', trim($baseCode)) ?? trim($baseCode);
        $existing = $this->sertifikatBoxes
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

    private function findSertifikatBoxesByLocation(string $lokasi): array
    {
        $normalized = strtoupper(trim($lokasi));
        if ($normalized === '') {
            return [];
        }

        return array_values(array_filter(
            $this->sertifikatBoxes->orderBy('id', 'asc')->findAll(),
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
