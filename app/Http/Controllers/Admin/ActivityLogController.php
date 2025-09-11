<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Activity::select([
                'id',
                'log_name',
                'description',
                'subject_id',
                'causer_id',
                'subject_type',
                'causer_type',
                'created_at'
            ])->with(['causer', 'subject'])->orderBy('created_at', 'desc');

            if ($request->filled('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $data->where(function ($q) use ($search) {
                    $q->where('log_name', 'like', '%' . $search . '%');
                });
            }

            if ($request->filled('pengguna')) {
                $data->whereHasMorph(
                    'causer',
                    [User::class],
                    function ($query, $type) use ($request) {
                        if ($type === User::class) {
                            $query->where('name', 'like', '%' . $request->pengguna . '%');
                        }
                    }
                );
            }

            if ($request->filled('rentang_waktu_dari')) {
                $data->where('created_at', '>=', Carbon::createFromFormat('d/m/Y', $request->rentang_waktu_dari)->translatedFormat('Y-m-d'));
            };
            if ($request->filled('rentang_waktu_sampai')) {
                $data->where('created_at', '<=', Carbon::createFromFormat('d/m/Y', $request->rentang_waktu_sampai)->translatedFormat('Y-m-d'));
            };


            $dataTable = DataTables::of($data);


            $dataTable = $dataTable
                ->addColumn('Nama Aktifitas', function ($row) {
                    $html = '
          <p class="f-light">' . ($row->log_name) . '</p>
          ';
                    return $html;
                })
                ->addColumn('Informasi', function ($row) {
                    $html = '
                    <p class="f-light">' . ($row->description) . '</p>
          ';
                    return $html;
                })
                ->addColumn('Pengguna', function ($row) {
                    $html = '
                        <span class="badge badge-light-primary">' . ($row->causer->name) . '</span>
                    ';
                    return $html;
                })
                ->addColumn('Subjek', function ($row) {
                    $html = '
                            <span class="badge badge-light-primary">ID ' . ($row->subject->id) . '</span>
                        ';
                    return $html;
                })
                ->editColumn('Waktu', function ($row) {
                    return '<p class="f-light">' . $row->created_at->translatedFormat('d/m/Y H:i:s') . '</p>';
                });

            $rawColumns = [];
            $rawColumns[] = 'Nama Aktifitas';
            $rawColumns[] = 'Informasi';
            $rawColumns[] = 'Pengguna';
            $rawColumns[] = 'Subjek';
            $rawColumns[] = 'Waktu';

            return $dataTable->rawColumns($rawColumns)->make(true);
        }

        return view('admin.activity-log.index');
    }
}
