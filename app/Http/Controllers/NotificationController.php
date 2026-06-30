<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Ambil daftar notifikasi untuk user login
    public function index(Request $request)
    {
        $userId = $request->user()->id_user;
        $today = date('Y-m-d');
        $warningThreshold = date('Y-m-d', strtotime('+2 days'));

        // Cek peminjaman aktif yang mendekati batas waktu (Hanya untuk Mahasiswa)
        if ($request->user()->role === 'mahasiswa') {
            $activeBorrowings = \DB::table('peminjamans')
                ->join('books', 'peminjamans.id_buku', '=', 'books.id_buku')
                ->where('peminjamans.id_user', $userId)
                ->whereIn('peminjamans.status', ['dipinjam', 'disetujui'])
                ->where('peminjamans.tanggal_kembali', '<=', $warningThreshold)
                ->select('peminjamans.id_transaksi', 'peminjamans.tanggal_kembali', 'books.judul')
                ->get();

            foreach ($activeBorrowings as $borrowing) {
                $exists = \DB::table('notifications')
                    ->where('id_transaksi', $borrowing->id_transaksi)
                    ->where('type', 'due_warning')
                    ->exists();

                if (!$exists) {
                    $diff = strtotime($borrowing->tanggal_kembali) - strtotime($today);
                    $daysLeft = round($diff / (60 * 60 * 24));

                    if ($daysLeft < 0) {
                        $title = "Batas Waktu Terlewati!";
                        $message = "Batas waktu pengembalian buku '{$borrowing->judul}' telah lewat pada " . date('d-m-Y', strtotime($borrowing->tanggal_kembali)) . ". Harap segera kembalikan ke perpustakaan.";
                    } else if ($daysLeft === 0) {
                        $title = "Pengembalian Hari Ini!";
                        $message = "Buku '{$borrowing->judul}' harus dikembalikan hari ini. Harap segera kembalikan ke perpustakaan.";
                    } else {
                        $title = "Peringatan Pengembalian";
                        $message = "Batas waktu pengembalian buku '{$borrowing->judul}' tinggal {$daysLeft} hari lagi (" . date('d-m-Y', strtotime($borrowing->tanggal_kembali)) . ").";
                    }

                    \DB::table('notifications')->insert([
                        'id_user' => $userId,
                        'id_transaksi' => $borrowing->id_transaksi,
                        'title' => $title,
                        'message' => $message,
                        'type' => 'due_warning',
                        'is_read' => false,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        // Cek pengajuan peminjaman menunggu persetujuan (Hanya untuk Admin)
        if ($request->user()->role === 'admin') {
            $pendingBorrowings = \DB::table('peminjamans')
                ->join('users', 'peminjamans.id_user', '=', 'users.id_user')
                ->join('mahasiswas', 'users.id_user', '=', 'mahasiswas.id_user')
                ->join('books', 'peminjamans.id_buku', '=', 'books.id_buku')
                ->where('peminjamans.status', 'menunggu')
                ->select('peminjamans.id_transaksi', 'mahasiswas.nama', 'books.judul', 'peminjamans.created_at')
                ->get();

            foreach ($pendingBorrowings as $borrowing) {
                $exists = \DB::table('notifications')
                    ->where('id_user', $userId)
                    ->where('id_transaksi', $borrowing->id_transaksi)
                    ->where('type', 'borrow_request')
                    ->exists();

                if (!$exists) {
                    \DB::table('notifications')->insert([
                        'id_user' => $userId,
                        'id_transaksi' => $borrowing->id_transaksi,
                        'title' => 'Pengajuan Peminjaman',
                        'message' => "Mahasiswa '{$borrowing->nama}' mengajukan peminjaman buku '{$borrowing->judul}'.",
                        'type' => 'borrow_request',
                        'is_read' => false,
                        'created_at' => $borrowing->created_at ?: date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        $notifications = \DB::table('notifications')
            ->where('id_user', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $notifications
        ], 200);
    }

    // Tandai satu notifikasi sebagai telah dibaca
    public function markAsRead(Request $request, $id)
    {
        $userId = $request->user()->id_user;

        $notification = Notification::where('id_notification', $id)
            ->where('id_user', $userId)
            ->first();

        if (!$notification) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notifikasi ditandai sebagai telah dibaca'
        ], 200);
    }

    // Tandai semua notifikasi milik user sebagai telah dibaca
    public function markAllAsRead(Request $request)
    {
        $userId = $request->user()->id_user;

        Notification::where('id_user', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Semua notifikasi ditandai sebagai telah dibaca'
        ], 200);
    }

    // Hapus satu notifikasi
    public function destroy(Request $request, $id)
    {
        $userId = $request->user()->id_user;

        $notification = Notification::where('id_notification', $id)
            ->where('id_user', $userId)
            ->first();

        if (!$notification) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Notifikasi berhasil dihapus'
        ], 200);
    }

    // Hapus semua notifikasi milik user
    public function destroyAll(Request $request)
    {
        $userId = $request->user()->id_user;

        Notification::where('id_user', $userId)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Semua notifikasi berhasil dihapus'
        ], 200);
    }
}
