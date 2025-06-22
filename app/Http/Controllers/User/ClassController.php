<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ClassController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:vice-principal']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SchoolClass::filter($request->all());

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('', function ($row) {
                    $html = '';
                    return $html;
                })
                ->addColumn('Nama', function ($row) {
                    $html = '
                        <div class="product-names">
                            <p>' . $row->name . '</p>
                        </div>
                    ';
                    return $html;
                })
                ->addColumn('Tingkat', function ($row) {
                    $html = '
                        <p class="f-light">' . $row->level . '</p>
                    ';
                    return $html;
                })
                ->addColumn('Jurusan', function ($row) {
                    $html = '
                        <span class="badge badge-light-primary">' . $row->major . '</span>
                    ';
                    return $html;
                })
                ->addColumn('Kapasitas', function ($row) {
                    $html = '
                        <p class="f-light">' . $row->capacity . '</p>
                    ';
                    return $html;
                })
                ->addColumn('Aksi', function ($row) {
                    $html = '
                        <div class="common-align gap-2 justify-content-start"> <a
                            class="square-white" href="#!"><svg>
                                <use
                                    href="' . asset('assets/svg/icon-sprite.svg#edit-content') . '">
                                </use>
                            </svg></a><a class="square-white trash-3" href="#!"><svg>
                                <use
                                    href="' . asset('assets/svg/icon-sprite.svg#trash1') . '">
                                </use>
                            </svg></a>
                        </div>
                    ';
                    return $html;
                })
                ->rawColumns(['', 'Nama', 'Tingkat', 'Jurusan', 'Kapasitas', 'Aksi'])
                ->make(true);
        } else {
            $classes = SchoolClass::filter(request()->all())->paginate(10);
            $levels = SchoolClass::select('level')->distinct()->get();
            $majors = SchoolClass::select('major')->distinct()->get();
            return view('user.class.index', [
                'classes' => $classes,
                'levels' => $levels,
                'majors' => $majors
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
