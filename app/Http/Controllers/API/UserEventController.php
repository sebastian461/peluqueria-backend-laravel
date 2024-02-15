<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\userEvent\CreateUserEventResource;
use App\Http\Resources\userEvent\GetUserEventResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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
      ->whereBetween("event_user.start", [$start, $end])
      ->get();

    $spreadsheet = $this->generateExcel($events);

    $writer = new Xlsx($spreadsheet);
    $archivoExcel = 'report.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $archivoExcel . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
  }

  public function reportForId(Request $request, $id, $start, $end)
  {
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
      ->whereBetween("event_user.start", [$start, $end])
      ->where("users.id", $id)
      ->get();

    $spreadsheet = $this->generateExcel($events);

    $writer = new Xlsx($spreadsheet);
    $archivoExcel = 'report.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $archivoExcel . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
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
}
