<?php

namespace App\Http\Controllers;

use App\Services\AgendaService;
use Illuminate\Http\Request;

class ScanAgendaInvitationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(AgendaService $agendaService, string $agendaId, string $code)
    {
        $agenda = $agendaService->findById($agendaId, $code);
        $metaData = $agendaService->getMetaData($agenda);

        abort_if(blank($agenda), 404);

        return view('scan.index', compact('agenda', 'metaData'));
    }
}
