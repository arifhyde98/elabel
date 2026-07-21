<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BpkbDeleteModel;
use App\Models\BpkbModel;
use App\Models\BoxModel;
use App\Models\BoxYearModel;
use App\Models\UserModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BpkbController extends BaseController
{
    private BpkbModel $bpkb;
    private BoxModel $boxes;
    private BpkbDeleteModel $deletes;
    private UserModel $users;

    public function __construct()
    {
        helper(['form']);
        $this->bpkb    = new BpkbModel();
        $this->boxes   = new BoxModel();
        $this->deletes = new BpkbDeleteModel();
        $this->users   = new UserModel();
    }

    public function index(?string $type = null): string
    {
        $vehicleType = $this->normalizeVehicleType($type);
        $vehicleLabel = $this->vehicleLabel($vehicleType);
        $query = trim((string) $this->request->getGet('q'));

        $builder = $this->bpkb
            ->select('bpkb.*, boxes.box_code, users.name as input_name')
            ->join('boxes', 'boxes.id = bpkb.box_id')
            ->join('users', 'users.id = bpkb.input_by')
            ->where('bpkb.status !=', 'Dihapus');
        if ($vehicleType !== null) {
            $builder->where('bpkb.vehicle_type', $vehicleType);
        }
        if ($query !== '') {
            $builder->groupStart()
                ->like('bpkb.plate_number', $query)
                ->orLike('bpkb.no_bpkb', $query)
                ->orLike('bpkb.no_rangka', $query)
                ->orLike('bpkb.no_mesin', $query)
                ->orLike('bpkb.merek', $query)
                ->orLike('bpkb.tipe', $query)
                ->orLike('bpkb.isi_silinder', $query)
                ->orLike('bpkb.warna', $query)
                ->orLike('bpkb.pengguna', $query)
                ->orLike('boxes.box_code', $query)
                ->groupEnd();
        }
        $items = $builder
            ->orderBy('bpkb.year', 'desc')
            ->orderBy('bpkb.plate_number', 'asc')
            ->findAll();

        $yearsBuilder = (new BoxYearModel())
            ->select('box_years.year')
            ->distinct()
            ->join('boxes', 'boxes.id = box_years.box_id');
        if ($vehicleType !== null) {
            $yearsBuilder->where('boxes.vehicle_type', $vehicleType);
        }
        $years = $yearsBuilder
            ->orderBy('box_years.year', 'desc')
            ->findAll();
        $yearOptions = array_map(static fn (array $row): int => (int) $row['year'], $years);

        return view('admin/bpkb/index', [
            'items'      => $items,
            'years'      => $yearOptions,
            'vehicleType' => $vehicleType,
            'vehicleLabel' => $vehicleLabel,
            'vehicleRoute' => $vehicleType ? $this->routeSegment($vehicleType) : null,
            'activeMenu' => $vehicleType === 'R2' ? 'bpkb_motor' : ($vehicleType === 'R4' ? 'bpkb_mobil' : 'bpkb'),
            'searchQuery' => $query,
        ]);
    }

    public function create(?string $type = null): string
    {
        $vehicleType = $this->normalizeVehicleType($type);
        $vehicleLabel = $this->vehicleLabel($vehicleType);
        $boxes = $this->boxes->orderBy('box_code', 'asc')->findAll();
        $yearOptions = $this->availableYears($vehicleType);

        return view('admin/bpkb/create', [
            'boxes'      => $boxes,
            'years'      => $yearOptions,
            'vehicleType' => $vehicleType,
            'vehicleLabel' => $vehicleLabel,
            'vehicleRoute' => $vehicleType ? $this->routeSegment($vehicleType) : null,
            'activeMenu' => $vehicleType === 'R2' ? 'bpkb_motor' : ($vehicleType === 'R4' ? 'bpkb_mobil' : 'bpkb'),
        ]);
    }

    public function edit(int $id): string
    {
        $item = $this->bpkb
            ->select('bpkb.*, boxes.box_code')
            ->join('boxes', 'boxes.id = bpkb.box_id', 'left')
            ->where('bpkb.id', $id)
            ->first();
        if (! $item || $item['status'] === 'Dihapus') {
            return redirect()->to(site_url('admin/bpkb'))->with('error', 'Data BPKB tidak ditemukan.');
        }

        $vehicleType = $this->normalizeVehicleType($item['vehicle_type'] ?? null);

        return view('admin/bpkb/edit', [
            'item'        => $item,
            'years'       => $this->availableYears(null),
            'vehicleType' => $vehicleType,
            'vehicleLabel' => $this->vehicleLabel($vehicleType),
            'vehicleRoute' => $vehicleType ? $this->routeSegment($vehicleType) : null,
            'activeMenu'  => $vehicleType === 'R2' ? 'bpkb_motor' : 'bpkb_mobil',
        ]);
    }

    public function show(int $id)
    {
        $item = $this->bpkb
            ->select('bpkb.*, boxes.box_code, boxes.location, users.name as input_name')
            ->join('boxes', 'boxes.id = bpkb.box_id')
            ->join('users', 'users.id = bpkb.input_by')
            ->where('bpkb.id', $id)
            ->first();

        if (! $item) {
            return redirect()->to(site_url('admin/bpkb'))->with('error', 'Data BPKB tidak ditemukan.');
        }

        return view('admin/bpkb/show', [
            'item'       => $item,
            'activeMenu' => ($item['vehicle_type'] ?? '') === 'R2' ? 'bpkb_motor' : 'bpkb_mobil',
        ]);
    }

    public function viewPdf(int $id)
    {
        $item = $this->bpkb->find($id);
        if (! $item || ! $item['pdf_path']) {
            $target = $item && ! empty($item['vehicle_type']) ? site_url('admin/bpkb/' . $item['vehicle_type']) : site_url('admin/bpkb');
            return redirect()->to($target)->with('error', 'File PDF tidak ditemukan.');
        }

        $path = WRITEPATH . $item['pdf_path'];
        if (! is_file($path)) {
            $target = ! empty($item['vehicle_type']) ? site_url('admin/bpkb/' . $item['vehicle_type']) : site_url('admin/bpkb');
            return redirect()->to($target)->with('error', 'File PDF tidak ditemukan.');
        }

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="bpkb-' . $id . '.pdf"')
            ->setBody(file_get_contents($path));
    }

    public function store()
    {
        $rules = [
            'year'         => 'required|integer',
            'vehicle_type' => 'required|in_list[R4,R2]',
            'plate_number' => 'required|max_length[20]',
            'no_bpkb'      => 'permit_empty|max_length[50]',
            'no_rangka'    => 'permit_empty|max_length[50]',
            'no_mesin'     => 'permit_empty|max_length[50]',
            'merek'        => 'permit_empty|max_length[100]',
            'tipe'         => 'permit_empty|max_length[100]',
            'isi_silinder' => 'permit_empty|max_length[50]',
            'warna'        => 'permit_empty|max_length[100]',
            'pengguna'     => 'permit_empty|max_length[100]',
            'pdf'          => 'if_exist|max_size[pdf,5120]|ext_in[pdf,pdf]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $year = (int) $this->request->getPost('year');
        $vehicleType = $this->normalizeVehicleType((string) $this->request->getPost('vehicle_type'));
        $identity = $this->normalizeBpkbIdentity([
            'plate_number' => (string) $this->request->getPost('plate_number'),
            'no_bpkb'      => (string) $this->request->getPost('no_bpkb'),
            'no_rangka'    => (string) $this->request->getPost('no_rangka'),
            'no_mesin'     => (string) $this->request->getPost('no_mesin'),
        ]);
        $duplicate = $this->findDuplicateBpkb($identity, $year);
        if ($duplicate !== null) {
            return redirect()->back()->withInput()->with('error', $this->duplicateBpkbMessage($duplicate));
        }

        $yearAvailableBuilder = (new BoxYearModel())
            ->join('boxes', 'boxes.id = box_years.box_id')
            ->where('box_years.year', $year);
        if ($vehicleType !== null) {
            $yearAvailableBuilder->where('boxes.vehicle_type', $vehicleType);
        }
        $yearAvailable = $yearAvailableBuilder->countAllResults() > 0;
        if (! $yearAvailable) {
            return redirect()->back()->withInput()->with('error', 'Tahun dokumen belum tersedia di data box.');
        }
        $boxId = $this->resolveBoxForYear($year, $vehicleType);
        if (! $boxId) {
            return redirect()->back()->withInput()->with('error', 'Box untuk tahun tersebut belum tersedia.');
        }

        $file = $this->request->getFile('pdf');
        $path = null;
        if ($file && $file->isValid()) {
            $box = $this->boxes->find((int) $boxId);
            $path = $this->storeBpkbPdf(
                $file,
                $identity['plate_number'],
                $year,
                (string) ($box['box_code'] ?? '')
            );
        }

        $newId = $this->bpkb->insert([
            'box_id'       => (int) $boxId,
            'year'         => $year,
            'vehicle_type' => $vehicleType ?? 'R4',
            'plate_number' => $identity['plate_number'],
            'no_bpkb'      => $identity['no_bpkb'],
            'no_rangka'    => $identity['no_rangka'],
            'no_mesin'     => $identity['no_mesin'],
            'merek'        => $this->normalizeTextField((string) $this->request->getPost('merek')),
            'tipe'         => $this->normalizeTextField((string) $this->request->getPost('tipe')),
            'isi_silinder' => $this->normalizeTextField((string) $this->request->getPost('isi_silinder')),
            'warna'        => $this->normalizeTextField((string) $this->request->getPost('warna')),
            'pengguna'     => $this->normalizeTextField((string) $this->request->getPost('pengguna')),
            'status'       => 'Tersedia',
            'pdf_path'     => $path,
            'input_by'     => (int) session()->get('user_id'),
        ]);
        $this->logActivity('create', 'BPKB', 'Menambahkan BPKB ' . $identity['plate_number'] . ' tahun ' . $year . '.', 'bpkb', (int) $newId);

        return redirect()->to(site_url('admin/bpkb/' . $this->routeSegment($vehicleType ?? 'R4')))->with('success', 'Data BPKB berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $item = $this->bpkb->find($id);
        if (! $item || $item['status'] === 'Dihapus') {
            return redirect()->to(site_url('admin/bpkb'))->with('error', 'Data BPKB tidak ditemukan.');
        }

        $rules = [
            'year'         => 'required|integer',
            'vehicle_type' => 'required|in_list[R4,R2]',
            'plate_number' => 'required|max_length[20]',
            'no_bpkb'      => 'permit_empty|max_length[50]',
            'no_rangka'    => 'permit_empty|max_length[50]',
            'no_mesin'     => 'permit_empty|max_length[50]',
            'merek'        => 'permit_empty|max_length[100]',
            'tipe'         => 'permit_empty|max_length[100]',
            'isi_silinder' => 'permit_empty|max_length[50]',
            'warna'        => 'permit_empty|max_length[100]',
            'pengguna'     => 'permit_empty|max_length[100]',
            'pdf'          => 'if_exist|max_size[pdf,5120]|ext_in[pdf,pdf]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $year = (int) $this->request->getPost('year');
        $vehicleType = $this->normalizeVehicleType((string) $this->request->getPost('vehicle_type'));
        $identity = $this->normalizeBpkbIdentity([
            'plate_number' => (string) $this->request->getPost('plate_number'),
            'no_bpkb'      => (string) $this->request->getPost('no_bpkb'),
            'no_rangka'    => (string) $this->request->getPost('no_rangka'),
            'no_mesin'     => (string) $this->request->getPost('no_mesin'),
        ]);
        $duplicate = $this->findDuplicateBpkb($identity, $year, $id);
        if ($duplicate !== null) {
            return redirect()->back()->withInput()->with('error', $this->duplicateBpkbMessage($duplicate));
        }

        $yearAvailable = $this->isYearAvailable($year, $vehicleType);
        if (! $yearAvailable) {
            return redirect()->back()->withInput()->with('error', 'Tahun dokumen belum tersedia di data box.');
        }

        $boxId = $this->resolveBoxForYear($year, $vehicleType, $id);
        if (! $boxId) {
            return redirect()->back()->withInput()->with('error', 'Box untuk tahun tersebut belum tersedia.');
        }

        $pdfPath = $item['pdf_path'] ?? null;
        $file = $this->request->getFile('pdf');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $oldPdfPath = $pdfPath;
            $box = $this->boxes->find((int) $boxId);
            $pdfPath = $this->storeBpkbPdf(
                $file,
                $identity['plate_number'],
                $year,
                (string) ($box['box_code'] ?? '')
            );

            if ($oldPdfPath && $oldPdfPath !== $pdfPath) {
                $oldAbsolutePath = WRITEPATH . $oldPdfPath;
                if (is_file($oldAbsolutePath)) {
                    @unlink($oldAbsolutePath);
                }
            }
        }

        $this->bpkb->update($id, [
            'box_id'       => (int) $boxId,
            'year'         => $year,
            'vehicle_type' => $vehicleType ?? 'R4',
            'plate_number' => $identity['plate_number'],
            'no_bpkb'      => $identity['no_bpkb'],
            'no_rangka'    => $identity['no_rangka'],
            'no_mesin'     => $identity['no_mesin'],
            'merek'        => $this->normalizeTextField((string) $this->request->getPost('merek')),
            'tipe'         => $this->normalizeTextField((string) $this->request->getPost('tipe')),
            'isi_silinder' => $this->normalizeTextField((string) $this->request->getPost('isi_silinder')),
            'warna'        => $this->normalizeTextField((string) $this->request->getPost('warna')),
            'pengguna'     => $this->normalizeTextField((string) $this->request->getPost('pengguna')),
            'pdf_path'     => $pdfPath,
        ]);
        $this->logActivity('update', 'BPKB', 'Mengubah BPKB ' . $identity['plate_number'] . ' tahun ' . $year . '.', 'bpkb', $id);

        return redirect()->to(site_url('admin/bpkb/' . $this->routeSegment($vehicleType ?? 'R4')))->with('success', 'Data BPKB berhasil diperbarui.');
    }

    public function import()
    {
        $rules = [
            'import_file' => 'uploaded[import_file]|max_size[import_file,5120]|ext_in[import_file,xlsx,xls,csv]',
        ];

        $redirect = $this->bpkbIndexRedirect((string) $this->request->getPost('vehicle_type_context'));

        if (! $this->validate($rules)) {
            return redirect()->to($redirect)
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('openModal', 'import');
        }

        $file = $this->request->getFile('import_file');
        if (! $file || ! $file->isValid()) {
            return redirect()->to($redirect)
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

            $importPath = $importDir . DIRECTORY_SEPARATOR . uniqid('bpkb-import-', true) . '.' . $extension;
            $file->move($importDir, basename($importPath));

            $reader = IOFactory::createReaderForFile($importPath);
            if ($extension === 'csv' && method_exists($reader, 'setReadDataOnly')) {
                $reader->setReadDataOnly(true);
            }
            $spreadsheet = $reader->load($importPath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray('', true, true, true);
        } catch (\Throwable $exception) {
            if ($importPath && is_file($importPath)) {
                @unlink($importPath);
            }

            log_message('error', 'BPKB import failed: {message}', [
                'message' => $exception->getMessage(),
            ]);

            $errorMessage = 'File import tidak dapat dibaca. Gunakan format XLSX, XLS, atau CSV yang valid.';
            if (strtolower((string) ENVIRONMENT) === 'development') {
                $errorMessage .= ' Detail: ' . $exception->getMessage();
            }

            return redirect()->to($redirect)
                ->with('error', $errorMessage)
                ->with('openModal', 'import');
        }

        if ($importPath && is_file($importPath)) {
            @unlink($importPath);
        }

        if (count($rows) < 2) {
            return redirect()->to($redirect)
                ->with('error', 'File import tidak berisi data.')
                ->with('openModal', 'import');
        }

        $contextType = $this->normalizeVehicleType((string) $this->request->getPost('vehicle_type_context'));
        $parsedRows = $this->extractImportRows($rows, $contextType);

        $successCount = 0;
        $errorMessages = [];

        foreach ($parsedRows as $rowNumber => $row) {
            if ($this->isImportRowEmpty($row)) {
                continue;
            }

            $vehicleType = $this->normalizeVehicleType($row['vehicle_type'] !== '' ? $row['vehicle_type'] : ($contextType ?? ''));
            $year = (int) $row['year'];
            $identity = $this->normalizeBpkbIdentity([
                'plate_number' => $row['plate_number'],
                'no_bpkb'      => $row['no_bpkb'],
                'no_rangka'    => $row['no_rangka'],
                'no_mesin'     => $row['no_mesin'],
            ]);
            $plateNumber = $identity['plate_number'];

            if ($vehicleType === null) {
                $errorMessages[] = 'Baris ' . $rowNumber . ': jenis kendaraan harus R4 atau R2.';
                continue;
            }
            if ($year <= 0) {
                $errorMessages[] = 'Baris ' . $rowNumber . ': tahun pembuatan wajib diisi.';
                continue;
            }
            if ($plateNumber === '') {
                $errorMessages[] = 'Baris ' . $rowNumber . ': No. Polisi wajib diisi.';
                continue;
            }
            if (! $this->isYearAvailable($year, $vehicleType)) {
                $errorMessages[] = 'Baris ' . $rowNumber . ': tahun ' . $year . ' belum tersedia untuk ' . $vehicleType . '.';
                continue;
            }
            $duplicate = $this->findDuplicateBpkb($identity, $year);
            if ($duplicate !== null) {
                $errorMessages[] = 'Baris ' . $rowNumber . ': ' . $this->duplicateBpkbMessage($duplicate);
                continue;
            }

            $boxId = $this->resolveBoxForYear($year, $vehicleType);
            if (! $boxId) {
                $errorMessages[] = 'Baris ' . $rowNumber . ': box untuk tahun ' . $year . ' tidak tersedia.';
                continue;
            }

            $result = $this->bpkb->insert([
                'box_id'       => (int) $boxId,
                'year'         => $year,
                'vehicle_type' => $vehicleType,
                'plate_number' => $plateNumber,
                'no_bpkb'      => $identity['no_bpkb'],
                'no_rangka'    => $identity['no_rangka'],
                'no_mesin'     => $identity['no_mesin'],
                'merek'        => $this->normalizeTextField($row['merek']),
                'tipe'         => $this->normalizeTextField($row['tipe']),
                'isi_silinder' => $this->normalizeTextField($row['isi_silinder']),
                'warna'        => $this->normalizeTextField($row['warna']),
                'pengguna'     => $this->normalizeTextField($row['pengguna']),
                'status'       => 'Tersedia',
                'pdf_path'     => null,
                'input_by'     => (int) session()->get('user_id'),
            ]);

            if ($result === false) {
                $errors = $this->bpkb->errors();
                $errorMessages[] = 'Baris ' . $rowNumber . ': ' . implode(', ', $errors ?: ['gagal disimpan']);
                continue;
            }

            $successCount++;
        }

        if ($successCount === 0 && $errorMessages !== []) {
            return redirect()->to($redirect)
                ->with('error', implode(' ', array_slice($errorMessages, 0, 5)))
                ->with('openModal', 'import');
        }

        $message = $successCount . ' data BPKB berhasil diimport.';
        if ($errorMessages !== []) {
            $message .= ' ' . min(count($errorMessages), 5) . ' baris dilewati: ' . implode(' ', array_slice($errorMessages, 0, 5));
        }
        $this->logActivity('import', 'BPKB', 'Mengimport ' . $successCount . ' data BPKB.', 'bpkb', null);

        return redirect()->to($redirect)->with('success', $message);
    }

    public function downloadImportTemplate()
    {
        $type = (string) $this->request->getGet('type');
        $vehicleType = $this->normalizeVehicleType($type !== '' ? $type : null);
        $label = $vehicleType ? strtolower($vehicleType) : 'semua';
        $filename = 'format-import-bpkb-' . $label . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Import BPKB');
        $sheet->fromArray([
            ['No', 'No. Polisi', 'No. BPKB', 'No. Rangka', 'No. Mesin', 'Merek', 'Tipe', 'Isi Silinder', 'Warna', 'Pengguna', 'Tahun', 'Jenis'],
            [1, 'DN 1234 AB', 'BPKB001', 'RANGKA001', 'MESIN001', 'Toyota', 'Avanza', '1500 CC', 'Hitam', 'Sekretariat', '2024', $vehicleType ?? 'R4'],
        ], null, 'A1');

        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'bpkb-import-template-');
        if ($tempFile === false) {
            return $this->response;
        }

        $writer->save($tempFile);
        $contents = file_get_contents($tempFile);
        @unlink($tempFile);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setBody($contents ?: '');
    }

    public function export()
    {
        $type = (string) $this->request->getGet('type');
        $vehicleType = $this->normalizeVehicleType($type !== '' ? $type : null);

        $builder = $this->bpkb
            ->select('bpkb.plate_number, bpkb.no_bpkb, bpkb.no_rangka, bpkb.no_mesin, bpkb.merek, bpkb.tipe, bpkb.isi_silinder, bpkb.warna, bpkb.pengguna, bpkb.year, bpkb.vehicle_type, bpkb.status, boxes.box_code')
            ->join('boxes', 'boxes.id = bpkb.box_id')
            ->where('bpkb.status !=', 'Dihapus');
        if ($vehicleType !== null) {
            $builder->where('bpkb.vehicle_type', $vehicleType);
        }
        $items = $builder
            ->orderBy('bpkb.year', 'desc')
            ->orderBy('bpkb.plate_number', 'asc')
            ->findAll();

        $label = $vehicleType ? strtolower($vehicleType) : 'semua';
        $filename = 'bpkb-' . $label . '-' . date('Ymd') . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data BPKB');
        $lastColumn = 'N';
        $headerRow = 3;

        $sheet->mergeCells('A1:' . $lastColumn . '1');
        $sheet->setCellValue('A1', 'DATA BPKB ' . strtoupper($vehicleType ?? 'SEMUA'));
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 15,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color'    => ['argb' => 'FF1D4ED8'],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->fromArray([
            ['No', 'No. Polisi', 'No. BPKB', 'No. Rangka', 'No. Mesin', 'Merek', 'Tipe', 'Isi Silinder', 'Warna', 'Pengguna', 'Tahun', 'Jenis', 'Box', 'Status'],
        ], null, 'A' . $headerRow);

        $rowIndex = $headerRow + 1;
        $i = 1;
        foreach ($items as $row) {
            $sheet->fromArray([
                [
                    $i++,
                    $row['plate_number'] ?? '',
                    $row['no_bpkb'] ?? '',
                    $row['no_rangka'] ?? '',
                    $row['no_mesin'] ?? '',
                    $row['merek'] ?? '',
                    $row['tipe'] ?? '',
                    $row['isi_silinder'] ?? '',
                    $row['warna'] ?? '',
                    $row['pengguna'] ?? '',
                    $row['year'] ?? '',
                    $row['vehicle_type'] ?? '',
                    $row['box_code'] ?? '',
                    $row['status'] ?? '',
                ],
            ], null, 'A' . $rowIndex);
            $rowIndex++;
        }

        $lastRow = max($headerRow, $rowIndex - 1);
        $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color'    => ['argb' => 'FF0F766E'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFFFFFFF'],
                ],
            ],
        ]);
        $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFD1D5DB'],
                ],
            ],
        ]);
        if ($lastRow >= 4) {
            $sheet->getStyle('A4:' . $lastColumn . $lastRow)->getAlignment()
                ->setVertical(Alignment::VERTICAL_TOP)
                ->setWrapText(true);
            $sheet->getStyle('A4:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('K4:L' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('N4:N' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $widths = [
            'A' => 5,
            'B' => 13,
            'C' => 15,
            'D' => 18,
            'E' => 18,
            'F' => 13,
            'G' => 14,
            'H' => 12,
            'I' => 12,
            'J' => 18,
            'K' => 10,
            'L' => 8,
            'M' => 11,
            'N' => 11,
        ];
        foreach ($widths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        $sheet->getRowDimension($headerRow)->setRowHeight(24);
        for ($row = 4; $row <= $lastRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(22);
        }
        $sheet->setAutoFilter('A' . $headerRow . ':' . $lastColumn . $lastRow);
        $sheet->freezePane('A4');

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'bpkb-');
        if ($tempFile === false) {
            return $this->response;
        }
        $writer->save($tempFile);
        $contents = file_get_contents($tempFile);
        @unlink($tempFile);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setBody($contents ?: '');
    }

    public function delete(int $id)
    {
        $item = $this->bpkb->find($id);
        if (! $item) {
            return redirect()->to(site_url('admin/bpkb'))->with('error', 'Data BPKB tidak ditemukan.');
        }
        if ($item['status'] === 'Dihapus') {
            return redirect()->to(site_url('admin/bpkb/' . $this->routeSegment($item['vehicle_type'] ?? 'R4')))->with('error', 'Data BPKB sudah dihapus.');
        }

        $deletePassword = (string) $this->request->getPost('delete_password');
        if ($deletePassword === '' || ! $this->isCurrentUserPasswordValid($deletePassword)) {
            return redirect()->back()->with('error', 'Password login tidak valid. Data BPKB tidak dihapus.');
        }

        $reason = (string) $this->request->getPost('reason');
        $detail = (string) $this->request->getPost('reason_detail');
        $supportPath = null;

        $allowed = [
            'Di pinjam',
            'Penjualan',
            'Dihibahkan',
            'Kendaraan hilang',
            'Kendaraan tidak ditemukan',
            'Lainnya',
        ];

        if (! in_array($reason, $allowed, true)) {
            return redirect()->back()->with('error', 'Alasan penghapusan tidak valid.');
        }

        if ($reason === 'Lainnya' && $detail === '') {
            return redirect()->back()->with('error', 'Keterangan tambahan wajib diisi.');
        }

        $file = $this->request->getFile('support_doc');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $allowedExt = ['pdf', 'jpg', 'jpeg', 'png'];
            $ext = strtolower((string) $file->getExtension());
            if (! in_array($ext, $allowedExt, true)) {
                return redirect()->back()->with('error', 'Dokumen pendukung harus PDF/JPG/PNG.');
            }
            if ($file->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Ukuran dokumen pendukung maksimal 5MB.');
            }
            $uploadPath = WRITEPATH . 'uploads/bpkb_delete';
            if (! is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $supportPath = 'uploads/bpkb_delete/' . $newName;
        }

        $box = $this->boxes->find((int) ($item['box_id'] ?? 0));
        $this->deletes->where('bpkb_id', $id)->delete();
        $this->deletes->insert([
            'bpkb_id'       => $id,
            'box_id'        => (int) ($item['box_id'] ?? 0),
            'box_code'      => $box['box_code'] ?? null,
            'year'          => $item['year'] ?? null,
            'vehicle_type'  => $item['vehicle_type'] ?? null,
            'plate_number'  => $item['plate_number'] ?? null,
            'no_bpkb'       => $item['no_bpkb'] ?? null,
            'no_rangka'     => $item['no_rangka'] ?? null,
            'no_mesin'      => $item['no_mesin'] ?? null,
            'merek'         => $item['merek'] ?? null,
            'tipe'          => $item['tipe'] ?? null,
            'isi_silinder'  => $item['isi_silinder'] ?? null,
            'warna'         => $item['warna'] ?? null,
            'pengguna'      => $item['pengguna'] ?? null,
            'status'        => $item['status'] ?? null,
            'pdf_path'      => $item['pdf_path'] ?? null,
            'input_by'      => $item['input_by'] ?? null,
            'deleted_by'    => (int) session()->get('user_id'),
            'deleted_at'    => date('Y-m-d H:i:s'),
            'reason'        => $reason,
            'reason_detail' => $detail !== '' ? $detail : null,
            'support_doc_path' => $supportPath,
        ]);

        $this->bpkb->delete($id);
        $this->logActivity('delete', 'BPKB', 'Memindahkan BPKB ' . ($item['plate_number'] ?? '-') . ' ke BPKB keluar. Alasan: ' . $reason . '.', 'bpkb', $id);

        return redirect()->to(site_url('admin/bpkb-deleted'))->with('success', 'Data BPKB berhasil dihapus dan dihapus dari database.');
    }

    private function resolveBoxForYear(int $year, ?string $vehicleType, ?int $excludeBpkbId = null): ?int
    {
        if ($year <= 0) {
            return null;
        }

        $boxYearModel = new BoxYearModel();

        // Find boxes assigned to this year, order by oldest box id.
        $boxYearBuilder = $boxYearModel
            ->select('box_years.box_id, boxes.box_code, boxes.location')
            ->join('boxes', 'boxes.id = box_years.box_id')
            ->where('box_years.year', $year)
            ->orderBy('box_years.box_id', 'asc');
        if ($vehicleType !== null) {
            $boxYearBuilder->where('boxes.vehicle_type', $vehicleType);
        }
        $boxYears = $boxYearBuilder->findAll();

        if (empty($boxYears)) {
            return null;
        }

        foreach ($boxYears as $row) {
            $countBuilder = $this->bpkb
                ->where('box_id', $row['box_id'])
                ->where('status !=', 'Dihapus');
            if ($excludeBpkbId !== null) {
                $countBuilder->where('id !=', $excludeBpkbId);
            }
            $count = $countBuilder->countAllResults();
            if ($count < 55) {
                return (int) $row['box_id'];
            }
        }

        // All boxes full, create a new box with same base code + suffix.
        $baseCode = (string) $boxYears[0]['box_code'];
        $location = $boxYears[0]['location'] ?? null;

        $newCode = $this->nextBoxCodeSuffix($baseCode);

        $boxModel = new BoxModel();
        $newBoxId = $boxModel->insert([
            'box_code'   => $newCode,
            'location'   => $location,
            'vehicle_type' => $vehicleType ?? 'R4',
            'created_by' => (int) session()->get('user_id'),
        ], true);

        $boxYearModel->insert([
            'box_id' => (int) $newBoxId,
            'year'   => $year,
        ]);

        return (int) $newBoxId;
    }

    private function nextBoxCodeSuffix(string $baseCode): string
    {
        $boxModel = new BoxModel();
        $existing = $boxModel
            ->select('box_code')
            ->like('box_code', $baseCode, 'after')
            ->orderBy('box_code', 'asc')
            ->findAll();

        $max = 1;
        foreach ($existing as $row) {
            $code = (string) $row['box_code'];
            if (preg_match('/^' . preg_quote($baseCode, '/') . ' \\((\\d+)\\)$/', $code, $m)) {
                $num = (int) $m[1];
                if ($num > $max) {
                    $max = $num;
                }
            } elseif ($code === $baseCode && $max < 1) {
                $max = 1;
            }
        }

        $next = $max + 1;
        return $baseCode . ' (' . $next . ')';
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

    private function availableYears(?string $vehicleType): array
    {
        $yearsBuilder = (new BoxYearModel())
            ->select('box_years.year')
            ->distinct()
            ->join('boxes', 'boxes.id = box_years.box_id');
        if ($vehicleType !== null) {
            $yearsBuilder->where('boxes.vehicle_type', $vehicleType);
        }

        $years = $yearsBuilder
            ->orderBy('box_years.year', 'desc')
            ->findAll();

        return array_map(static fn (array $row): int => (int) $row['year'], $years);
    }

    private function isYearAvailable(int $year, ?string $vehicleType): bool
    {
        $yearAvailableBuilder = (new BoxYearModel())
            ->join('boxes', 'boxes.id = box_years.box_id')
            ->where('box_years.year', $year);
        if ($vehicleType !== null) {
            $yearAvailableBuilder->where('boxes.vehicle_type', $vehicleType);
        }

        return $yearAvailableBuilder->countAllResults() > 0;
    }

    private function bpkbIndexRedirect(string $type): string
    {
        $vehicleType = $this->normalizeVehicleType($type);

        if ($vehicleType === 'R2') {
            return site_url('admin/bpkb/r2');
        }
        if ($vehicleType === 'R4') {
            return site_url('admin/bpkb/r4');
        }

        return site_url('admin/bpkb');
    }

    private function extractImportRows(array $rows, ?string $contextType): array
    {
        $headerRow = array_shift($rows) ?: [];
        $headerMap = $this->buildImportHeaderMap($headerRow);
        $fallbackColumns = $this->importFallbackColumns($headerMap);
        $parsedRows = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $parsedRows[$rowNumber] = [
                'plate_number' => trim((string) $this->importCellValue($row, $headerMap, 'plate_number', $fallbackColumns['plate_number'])),
                'no_bpkb'      => trim((string) $this->importCellValue($row, $headerMap, 'no_bpkb', $fallbackColumns['no_bpkb'])),
                'no_rangka'    => trim((string) $this->importCellValue($row, $headerMap, 'no_rangka', $fallbackColumns['no_rangka'])),
                'no_mesin'     => trim((string) $this->importCellValue($row, $headerMap, 'no_mesin', $fallbackColumns['no_mesin'])),
                'merek'        => trim((string) $this->importCellValue($row, $headerMap, 'merek', $fallbackColumns['merek'])),
                'tipe'         => trim((string) $this->importCellValue($row, $headerMap, 'tipe', $fallbackColumns['tipe'])),
                'isi_silinder' => trim((string) $this->importCellValue($row, $headerMap, 'isi_silinder', $fallbackColumns['isi_silinder'])),
                'warna'        => trim((string) $this->importCellValue($row, $headerMap, 'warna', $fallbackColumns['warna'])),
                'pengguna'     => trim((string) $this->importCellValue($row, $headerMap, 'pengguna', $fallbackColumns['pengguna'])),
                'year'         => trim((string) $this->importCellValue($row, $headerMap, 'year', $fallbackColumns['year'])),
                'vehicle_type' => trim((string) $this->importCellValue($row, $headerMap, 'vehicle_type', $fallbackColumns['vehicle_type'])),
            ];

            if ($parsedRows[$rowNumber]['vehicle_type'] === '' && $contextType !== null) {
                $parsedRows[$rowNumber]['vehicle_type'] = $contextType;
            }
        }

        return $parsedRows;
    }

    private function buildImportHeaderMap(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $column => $header) {
            $normalizedHeader = strtolower(trim((string) $header));
            $normalizedHeader = preg_replace('/\s+/', ' ', $normalizedHeader ?? '');

            if (in_array($normalizedHeader, ['no. polisi', 'no polisi', 'plate_number', 'plat nomor'], true)) {
                $map['plate_number'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['no. bpkb', 'no bpkb', 'bpkb'], true)) {
                $map['no_bpkb'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['no. rangka', 'no rangka'], true)) {
                $map['no_rangka'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['no. mesin', 'no mesin'], true)) {
                $map['no_mesin'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['merek', 'brand'], true)) {
                $map['merek'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['tipe', 'type'], true)) {
                $map['tipe'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['isi silinder', 'isi_silinder', 'silinder', 'cc', 'kapasitas mesin'], true)) {
                $map['isi_silinder'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['warna', 'color', 'colour'], true)) {
                $map['warna'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['pengguna', 'user', 'pemakai'], true)) {
                $map['pengguna'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['tahun', 'tahun pembuatan', 'year'], true)) {
                $map['year'] = $column;
                continue;
            }
            if (in_array($normalizedHeader, ['jenis', 'jenis kendaraan', 'vehicle_type'], true)) {
                $map['vehicle_type'] = $column;
            }
        }

        return $map;
    }

    private function importFallbackColumns(array $headerMap): array
    {
        $usesExtendedLayout = isset($headerMap['merek'])
            || isset($headerMap['tipe'])
            || isset($headerMap['isi_silinder'])
            || isset($headerMap['warna'])
            || isset($headerMap['pengguna']);

        return [
            'plate_number' => 'B',
            'no_bpkb'      => 'C',
            'no_rangka'    => 'D',
            'no_mesin'     => 'E',
            'merek'        => $usesExtendedLayout ? 'F' : null,
            'tipe'         => $usesExtendedLayout ? 'G' : null,
            'isi_silinder' => $usesExtendedLayout ? 'H' : null,
            'warna'        => $usesExtendedLayout ? 'I' : null,
            'pengguna'     => $usesExtendedLayout ? 'J' : null,
            'year'         => $usesExtendedLayout ? 'K' : 'F',
            'vehicle_type' => $usesExtendedLayout ? 'L' : 'G',
        ];
    }

    private function importCellValue(array $row, array $headerMap, string $field, ?string $fallbackColumn): string
    {
        $column = $headerMap[$field] ?? $fallbackColumn;

        if ($column === null || $column === '') {
            return '';
        }

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

    private function normalizeTextField(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function isCurrentUserPasswordValid(string $password): bool
    {
        $userId = (int) session()->get('user_id');
        if ($userId <= 0 || $password === '') {
            return false;
        }

        $user = $this->users->find($userId);
        if (! $user || empty($user['password'])) {
            return false;
        }

        return password_verify($password, (string) $user['password']);
    }

    private function storeBpkbPdf($file, string $engineOrFallback, int $year, string $boxCode): string
    {
        $uploadPath = WRITEPATH . 'uploads/bpkb';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $extension = strtolower((string) $file->getExtension());
        if ($extension === '') {
            $extension = 'pdf';
        }

        $baseName = $this->buildBpkbPdfBaseName($engineOrFallback, $year, $boxCode);
        $newName = $baseName . '.' . $extension;
        $counter = 2;

        while (is_file($uploadPath . DIRECTORY_SEPARATOR . $newName)) {
            $newName = $baseName . '_' . $counter . '.' . $extension;
            $counter++;
        }

        $file->move($uploadPath, $newName);

        return 'uploads/bpkb/' . $newName;
    }

    private function buildBpkbPdfBaseName(string $engineOrFallback, int $year, string $boxCode): string
    {
        $engineToken = $this->filenameToken($engineOrFallback);
        $boxToken = $this->filenameToken($boxCode);

        if ($engineToken === '') {
            $engineToken = 'bpkb';
        }

        if ($boxToken === '') {
            $boxToken = 'box';
        }

        return $engineToken . '_' . $year . '_' . strtoupper($boxToken);
    }

    private function filenameToken(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '', $value) ?? '';

        return $value;
    }

    private function normalizeBpkbIdentity(array $data): array
    {
        $normalizeOptional = static function ($value): ?string {
            $value = strtoupper(trim((string) $value));
            if ($value === '' || $value === '-' || $value === '0') {
                return null;
            }

            return $value;
        };

        return [
            'plate_number' => strtoupper(trim((string) ($data['plate_number'] ?? ''))),
            'no_bpkb'      => $normalizeOptional($data['no_bpkb'] ?? null),
            'no_rangka'    => $normalizeOptional($data['no_rangka'] ?? null),
            'no_mesin'     => $normalizeOptional($data['no_mesin'] ?? null),
        ];
    }

    private function findDuplicateBpkb(array $identity, int $year, ?int $excludeId = null): ?array
    {
        $plateNumber = trim((string) ($identity['plate_number'] ?? ''));
        if ($plateNumber === '' || $year <= 0) {
            return null;
        }

        $builder = $this->bpkb
            ->where('plate_number', $plateNumber)
            ->where('year', $year);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        $duplicate = $builder->first();
        if ($duplicate === null) {
            return null;
        }

        return [
            'field'  => 'plate_number',
            'value'  => $plateNumber,
            'year'   => $year,
            'record' => $duplicate,
        ];
    }

    private function duplicateBpkbMessage(array $duplicate): string
    {
        $labels = [
            'plate_number' => 'No. Polisi',
            'no_bpkb'      => 'No. BPKB',
            'no_rangka'    => 'No. Rangka',
            'no_mesin'     => 'No. Mesin',
        ];

        $fieldLabel = $labels[$duplicate['field']] ?? $duplicate['field'];
        $record = $duplicate['record'] ?? [];
        $plate = (string) ($record['plate_number'] ?? '-');
        $year = (string) ($record['year'] ?? '-');

        return $fieldLabel . ' "' . $duplicate['value'] . '" tahun ' . $year . ' sudah terdaftar pada data BPKB.';
    }
}
