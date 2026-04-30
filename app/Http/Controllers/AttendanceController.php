<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('user');

        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        } else {
            $query->where('tanggal', Carbon::today());
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $attendances = $query->orderBy('tanggal', 'desc')->paginate(20);

        return view('attendance.index', compact('attendances'));
    }

    public function clockIn()
    {
        $today = Carbon::today();
        $existing = Attendance::where('user_id', auth()->id())
            ->where('tanggal', $today)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah absen masuk hari ini.');
        }

        Attendance::create([
            'user_id' => auth()->id(),
            'tanggal' => $today,
            'jam_masuk' => Carbon::now()->toTimeString(),
            'status' => 'hadir',
        ]);

        return back()->with('success', 'Absen masuk berhasil dicatat.');
    }

    public function clockOut()
    {
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', auth()->id())
            ->where('tanggal', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum absen masuk hari ini.');
        }

        if ($attendance->jam_pulang) {
            return back()->with('error', 'Anda sudah absen pulang hari ini.');
        }

        $attendance->update([
            'jam_pulang' => Carbon::now()->toTimeString(),
        ]);

        return back()->with('success', 'Absen pulang berhasil dicatat.');
    }
}
