<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\userEvent\CreateUserEventResource;
use App\Http\Resources\userEvent\GetUserEventResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserEventController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $event = DB::table("event_user")
      ->join("users", "event_user.user_id", "=", "users.id")
      ->join("events", "event_user.event_id", "=", "events.id")
      ->select(
        "event_user.id",
        "users.id as user_id",
        "users.name as user_name",
        "events.title",
        "events.amount",
        "event_user.start",
        "event_user.end"
      )
      ->get();

    return new GetUserEventResource($event);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request, string $id)
  {
    $user = $request->user();
    $user->events()->attach($id, ["start" => $request->start, "end" => $request->end]);

    $event = DB::table("event_user")
      ->join("users", "event_user.user_id", "=", "users.id")
      ->join("events", "event_user.event_id", "=", "events.id")
      ->select(
        "event_user.id",
        "users.id as user_id",
        "users.name as user_name",
        "events.title",
        "events.amount",
        "event_user.start",
        "event_user.end"
      )
      ->orderBy("event_user.id", "desc")->first();

    return new CreateUserEventResource($event);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Request $request, string $id)
  {
    $user = $request->user();
    $deleted = DB::table('event_user')->where("id", "=", $id)->delete();
    $user->events()->detach($id);

    return response()->json([
      "message" => "Deleted",
    ]);
  }

  public function report(Request $request, $start, $end)
  {

    $startFormated = $this->dateFormater($start);
    $endFormated = $this->dateFormater($end);

    $events = DB::table("event_user")
      ->join("users", "event_user.user_id", "=", "users.id")
      ->join("events", "event_user.event_id", "=", "events.id")
      ->select(
        "events.title",
        "events.amount",
        "users.name as user_name",
        "event_user.start",
        "event_user.end"
      )
      ->whereBetween("event_user.start", [$startFormated, $endFormated])
      ->get();

    $spreadsheet = $this->generateExcel($events);

    $filePath = base_path('documents\reporte.xlsx');

    $writer = new Xlsx($spreadsheet);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    $writer->save($filePath);

    return response()->download($filePath, 'reporte.xlsx');
  }

  public function reportForId(Request $request, $id, $start, $end)
  {
    $startFormated = $this->dateFormater($start);
    $endFormated = $this->dateFormater($end);

    $events = DB::table("event_user")
      ->join("users", "event_user.user_id", "=", "users.id")
      ->join("events", "event_user.event_id", "=", "events.id")
      ->select(
        "events.title",
        "events.amount",
        "users.name as user_name",
        "event_user.start",
        "event_user.end"
      )
      ->whereBetween("event_user.start", [$startFormated, $endFormated])
      ->where("users.id", $id)
      ->get();

    $spreadsheet = $this->generateExcel($events);

    $filePath = base_path('documents\reporte.xlsx');

    $writer = new Xlsx($spreadsheet);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    $writer->save($filePath);

    return response()->download($filePath, 'reporte.xlsx');
  }

  private function generateExcel($events)
  {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headerStyle = [
      'font' => ['bold' => true],
      'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ];
    $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

    $sheet->setCellValue('A1', 'Titulo');
    $sheet->setCellValue('B1', 'Precio');
    $sheet->setCellValue('C1', 'Encargado');
    $sheet->setCellValue('D1', 'Inicio');
    $sheet->setCellValue('E1', 'Fin');

    foreach ($events as $index => $event) {
      $row = $index + 2;
      $sheet->setCellValue('A' . $row, $event->title);
      $sheet->setCellValue('B' . $row, $event->amount);

      // Formatear la celda de precio como dinero
      $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');

      $sheet->setCellValue('C' . $row, $event->user_name);
      $sheet->setCellValue('D' . $row, date('d/m/Y H:i:s', strtotime($event->start)));
      $sheet->setCellValue('E' . $row, date('d/m/Y H:i:s', strtotime($event->end)));
    }

    foreach (range('A', 'E') as $column) {
      $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    return $spreadsheet;
  }

  private function dateFormater($date)
  {
    $date = Carbon::parse($date);

    $year = $date->year;
    $month = str_pad($date->month, 2, "0", STR_PAD_LEFT);
    $day = str_pad($date->day, 2, "0", STR_PAD_LEFT);
    $hour = str_pad($date->hour, 2, "0", STR_PAD_LEFT);
    $minute = str_pad($date->minute, 2, "0", STR_PAD_LEFT);
    $second = str_pad($date->second, 2, "0", STR_PAD_LEFT);
    $millisecond = str_pad($date->millisecond, 3, "0", STR_PAD_LEFT);

    $formatDate = "{$year}-{$month}-{$day}T{$hour}:{$minute}:{$second}.{$millisecond}Z";
    return $formatDate;
  }
}
