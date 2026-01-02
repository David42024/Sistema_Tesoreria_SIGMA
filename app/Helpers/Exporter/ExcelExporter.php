<?php
namespace App\Helpers\Exporter;

use App\Interfaces\ITableExporter;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromView;
use \Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ExcelAdapter implements FromView
{
    private string $template;
    private ExportRequest $request;

    public function __construct(string $template, ExportRequest $request)
    {
        $this->template = $template;
        $this->request = $request;
    }

    public function view(): View
    {
        return view($this->template, [
            'title' => $this->request->title(),
            'data' => $this->request->data(),
            'headers' => $this->request->headers(),
        ]);
    }
}

class ExcelExporter implements ITableExporter
{
    private string $template;

    public function __construct(string $template)
    {
        $this->template = $template;
    }

    public function export(ExportRequest $request)
    {
        $adapter = new ExcelAdapter($this->template, $request);
        return Excel::download($adapter, ($request->option('filename', 'table') . '.xlsx'));
    }

    public function exportAsResponse(ExportRequest $request)
    {
        return $this->export($request);
    }
}