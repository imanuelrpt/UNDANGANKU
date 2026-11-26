<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Owner;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicInvitationController;
use App\Http\Controllers\PublicRsvpController;
use App\Http\Controllers\PublicTicketController;
use App\Http\Controllers\PublicWishController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::middleware('throttle:public-page')->group(function (): void {
    Route::get('/u/{slug}', [PublicInvitationController::class, 'show'])->name('invitation.public');
    Route::get('/u/{slug}/{token}', [PublicInvitationController::class, 'show'])
        ->where('token', '[A-Za-z0-9_-]+')
        ->name('invitation.public.guest');
});

Route::post('/u/{slug}/rsvp', [PublicRsvpController::class, 'store'])
    ->middleware('throttle:rsvp')
    ->name('invitation.rsvp.store');

Route::post('/u/{slug}/wishes', [PublicWishController::class, 'store'])
    ->middleware('throttle:wishes')
    ->name('invitation.wish.store');

Route::get('/u/{slug}/wishes/load', [PublicWishController::class, 'load'])
    ->middleware('throttle:public-page')
    ->name('invitation.wish.load');

Route::get('/tiket/{token}/image', [PublicTicketController::class, 'image'])->name('ticket.image');
Route::get('/tiket/{token}/pdf', [PublicTicketController::class, 'pdf'])->name('ticket.pdf');
Route::get('/calendar/{event}/ics', [PublicTicketController::class, 'ics'])->name('calendar.ics');

Route::prefix('petugas')->group(function (): void {
    Route::get('/{token}', [StaffController::class, 'login'])->name('staff.login');
    Route::post('/{token}', [StaffController::class, 'authenticate'])
        ->middleware('throttle:staff-login')
        ->name('staff.login.submit');
    Route::get('/{token}/scanner', [StaffController::class, 'scanner'])->name('staff.scanner');
    Route::post('/{token}/scan', [StaffController::class, 'scan'])
        ->middleware('throttle:scan')
        ->name('staff.scan');
    Route::post('/{token}/manual', [StaffController::class, 'manual'])->name('staff.manual');
    Route::get('/{token}/attendance', [StaffController::class, 'attendance'])->name('staff.attendance');
    Route::post('/{token}/checkin/{checkin}', [StaffController::class, 'updateCheckin'])->name('staff.checkin.update');
});

Route::middleware(['auth', 'role:owner,admin'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/invitations', [Owner\InvitationController::class, 'index'])->name('invitations.index');
    Route::get('/dashboard/invitations/create', [Owner\InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/dashboard/invitations', [Owner\InvitationController::class, 'store'])->name('invitations.store');
    Route::get('/dashboard/invitations/{invitation}/edit', [Owner\InvitationController::class, 'edit'])->name('invitations.edit');
    Route::put('/dashboard/invitations/{invitation}', [Owner\InvitationController::class, 'update'])->name('invitations.update');
    Route::delete('/dashboard/invitations/{invitation}', [Owner\InvitationController::class, 'destroy'])->name('invitations.destroy');
    Route::post('/dashboard/invitations/{invitation}/publish', [Owner\InvitationController::class, 'publish'])->name('invitations.publish');
    Route::post('/dashboard/invitations/{invitation}/unpublish', [Owner\InvitationController::class, 'unpublish'])->name('invitations.unpublish');
    Route::post('/dashboard/invitations/{invitation}/duplicate', [Owner\InvitationController::class, 'duplicate'])->name('invitations.duplicate');
    Route::get('/dashboard/invitations/{invitation}/preview', [Owner\InvitationController::class, 'preview'])->name('invitations.preview');
    Route::get('/dashboard/invitations/{invitation}/stats', [DashboardController::class, 'stats'])->name('invitations.stats');

    Route::post('/dashboard/invitations/{invitation}/events', [Owner\EventController::class, 'store'])->name('events.store');
    Route::put('/dashboard/events/{event}', [Owner\EventController::class, 'update'])->name('events.update');
    Route::delete('/dashboard/events/{event}', [Owner\EventController::class, 'destroy'])->name('events.destroy');

    Route::post('/dashboard/invitations/{invitation}/photos', [Owner\PhotoController::class, 'store'])->name('photos.store');
    Route::delete('/dashboard/photos/{photo}', [Owner\PhotoController::class, 'destroy'])->name('photos.destroy');
    Route::post('/dashboard/invitations/{invitation}/photos/reorder', [Owner\PhotoController::class, 'reorder'])->name('photos.reorder');
    Route::post('/dashboard/invitations/{invitation}/hosts/{host}/photo', [Owner\PhotoController::class, 'hostPhoto'])->name('hosts.photo');

    Route::post('/dashboard/invitations/{invitation}/gifts', [Owner\GiftController::class, 'store'])->name('gifts.store');
    Route::delete('/dashboard/gifts/{gift}', [Owner\GiftController::class, 'destroy'])->name('gifts.destroy');

    Route::post('/dashboard/invitations/{invitation}/questions', [Owner\QuestionController::class, 'store'])->name('questions.store');
    Route::put('/dashboard/questions/{question}', [Owner\QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/dashboard/questions/{question}', [Owner\QuestionController::class, 'destroy'])->name('questions.destroy');

    Route::post('/dashboard/invitations/{invitation}/music', [Owner\InvitationController::class, 'uploadMusic'])->name('music.store');
    Route::delete('/dashboard/invitations/{invitation}/music', [Owner\InvitationController::class, 'deleteMusic'])->name('music.destroy');

    Route::get('/dashboard/invitations/{invitation}/guests', [Owner\GuestController::class, 'index'])->name('guests.index');
    Route::get('/dashboard/invitations/{invitation}/guests/create', [Owner\GuestController::class, 'create'])->name('guests.create');
    Route::post('/dashboard/invitations/{invitation}/guests', [Owner\GuestController::class, 'store'])->name('guests.store');
    Route::get('/dashboard/guests/{guest}/edit', [Owner\GuestController::class, 'edit'])->name('guests.edit');
    Route::put('/dashboard/guests/{guest}', [Owner\GuestController::class, 'update'])->name('guests.update');
    Route::delete('/dashboard/guests/{guest}', [Owner\GuestController::class, 'destroy'])->name('guests.destroy');
    Route::post('/dashboard/invitations/{invitation}/guests/bulk', [Owner\GuestController::class, 'bulk'])->name('guests.bulk');
    Route::post('/dashboard/invitations/{invitation}/guests/import', [Owner\GuestController::class, 'import'])->name('guests.import');
    Route::get('/dashboard/invitations/{invitation}/guests/template', [Owner\GuestController::class, 'template'])->name('guests.template');
    Route::get('/dashboard/invitations/{invitation}/guests/export', [Owner\GuestController::class, 'export'])->name('guests.export');
    Route::get('/dashboard/invitations/{invitation}/guests/export-pdf', [Owner\GuestController::class, 'exportPdf'])->name('guests.export-pdf');
    Route::post('/dashboard/guests/{guest}/sent', [Owner\GuestController::class, 'markSent'])->name('guests.sent');

    Route::get('/dashboard/invitations/{invitation}/wishes', [Owner\WishController::class, 'index'])->name('wishes.index');
    Route::put('/dashboard/wishes/{wish}', [Owner\WishController::class, 'update'])->name('wishes.update');
    Route::delete('/dashboard/wishes/{wish}', [Owner\WishController::class, 'destroy'])->name('wishes.destroy');

    Route::post('/dashboard/invitations/{invitation}/staff-links', [Owner\StaffLinkController::class, 'store'])->name('staff-links.store');
    Route::delete('/dashboard/staff-links/{staffLink}', [Owner\StaffLinkController::class, 'destroy'])->name('staff-links.destroy');

    Route::get('/dashboard/invitations/{invitation}/report/excel', [Owner\ReportController::class, 'excel'])->name('reports.excel');
    Route::get('/dashboard/invitations/{invitation}/report/pdf', [Owner\ReportController::class, 'pdf'])->name('reports.pdf');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function (): void {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [Admin\UserController::class, 'index'])->name('admin.users');
    Route::put('/users/{user}', [Admin\UserController::class, 'update'])->name('admin.users.update');
    Route::post('/users/{user}/toggle', [Admin\UserController::class, 'toggle'])->name('admin.users.toggle');
    Route::get('/invitations', [Admin\InvitationController::class, 'index'])->name('admin.invitations');
    Route::post('/invitations/{invitation}/takedown', [Admin\InvitationController::class, 'takedown'])->name('admin.invitations.takedown');
    Route::get('/themes', [Admin\ThemeController::class, 'index'])->name('admin.themes');
    Route::put('/themes/{theme}', [Admin\ThemeController::class, 'update'])->name('admin.themes.update');
    Route::get('/logs', [ActivityLogController::class, 'index'])->name('admin.logs');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
