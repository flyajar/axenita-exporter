<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PatientController extends Controller
{

    public function get(Request $request)
    {
        $data = $request->validate([
            'axenita_csrf_token'        => ['required', 'string'],
            'axenita_csrf_token_cookie' => ['required', 'string'],
            'axenita_auth_cookie'       => [
                'required',
                'string',
                'regex:/^axenita-authentication-[^=]+=.+$/',
            ],
            'page_size' => ['required', 'integer', 'min:1', 'max:5000'],
            'current_page'=> ['required', 'integer', 'min:1'],
        ]);

        $fixedSuffix = '60a80903-218e-4c3a-9302-73f6b6f2425e';

        $cookieHeader = implode('; ', [
            "axenita-csrf-token-cookie-$fixedSuffix={$data['axenita_csrf_token_cookie']}",
            trim($data['axenita_auth_cookie']),
            "axenita-language-$fixedSuffix=FRENCH",
        ]);

        $pageSize = (int) $data['page_size'];
        $currentPage = (int) $data['current_page'];

        $response = Http::withHeaders([
            'Accept'                 => 'application/json',
            'Content-Type'           => 'application/json;charset=UTF-8',
            'axenita-csrf-token'     => $data['axenita_csrf_token'],
            'axenita-ui-context'     => 'DEFAULT',
            'NOTIFICATION-CLIENT-ID' => 'f2d785a0-00bd-4b3d-a0ab-44081b5d4d8a',
            'userlanguage'           => 'fr',
            'workspacename'          => 'null',
            'Cookie'                 => $cookieHeader,
        ])->post(
            'https://aesgen01.axenita.ch/api/contacts/patient-search/search/local-patients',
            [
                'paginationParam' => [
                    'pageSize'         => $pageSize,
                    'currentPage'      => $currentPage,
                    'visiblePageCount' => 100,
                ],
                'queryString' => '',
                'sortParam' => [
                    'sortFields'     => [],
                    'sortDirections' => [],
                ],
                'filterParam' => [
                    'filterValueParams' => [],
                ],
            ]
        );

        if (! $response->successful()) {
            return back()->withErrors([
                'api' => 'Axenita API call failed: '.$response->status(),
            ]);
        }

        $json = $response->json();
        $results = data_get($json, 'data.result', []);

        $rows = collect($results)
            ->map(function (array $p) {
                return [
                    'axenita_id'  => $p['id'] ?? null,
                    'first_name'  => $p['firstname'] ?? null,
                    'last_name'   => $p['lastname'] ?? null,
                    'gender'      => $p['gender'] ?? null,
                    'birth_date'  => !empty($p['birthday'])
                        ? Carbon::parse($p['birthday'])->toDateString()
                        : null,
                    'email'       => null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            })
            ->filter(fn ($row) => !empty($row['axenita_id']))
            ->values()
            ->all();

        // Insert new, update existing (by axenita_id)
        Patient::upsert(
            $rows,
            ['axenita_id'],
            ['first_name', 'last_name', 'gender', 'birth_date', 'email', 'updated_at']
        );

        return back()->with('success', 'Saved patients: ' . count($rows));
    }
}
