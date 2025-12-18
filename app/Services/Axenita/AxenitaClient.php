<?php

namespace App\Services\Axenita;

use Illuminate\Http\Client\PendingRequest;
use RuntimeException;

class AxenitaClient
{
    public function __construct(
        private readonly AxenitaSession $session,
    ) {}

    private function baseUrl(): string
    {
        return rtrim(config('axenita.base_url'), '/');
    }

    private function cookieNamespace(): string
    {
        $ns = config('axenita.cookie_namespace');
        if (!$ns) {
            throw new RuntimeException('AXENITA_COOKIE_NAMESPACE is not set.');
        }
        return $ns;
    }

    private function cookieHeader(array $cookies): string
    {
        // Build: "name=value; name2=value2"
        $parts = [];
        foreach ($cookies as $name => $value) {
            if ($value === null || $value === '') continue;
            $parts[] = "{$name}={$value}";
        }
        return implode('; ', $parts);
    }

    private function buildPendingRequest(): PendingRequest
    {
        $ns = $this->cookieNamespace();
        $saved = $this->session->get();

        // Seed from config if session empty (optional)
        $csrfCookie = $saved['csrf_cookie'] ?? config('axenita.seed_cookies.csrf_cookie');
        $csrfHeader = $saved['csrf_header_token'] ?? config('axenita.csrf_header_token');

        $cookies = [
            "axenita-csrf-token-cookie-{$ns}" => $csrfCookie,
            "axenita-language-{$ns}"          => config('axenita.seed_cookies.language_cookie', 'FRENCH'),
            // Add more Axenita cookies here as you discover them (session cookie, etc.)
        ];

        return Http::baseUrl($this->baseUrl())
            ->acceptJson()
            ->withHeaders([
                'Content-Type' => 'application/json;charset=UTF-8',
                'axenita-ui-context' => config('axenita.ui_context', 'DEFAULT'),
                'NOTIFICATION-CLIENT-ID' => config('axenita.notification_client_id'),
                'userlanguage' => config('axenita.language', 'fr'),
                'workspacename' => config('axenita.workspace', 'null'),
                // CSRF header token (if required by this endpoint)
                ...( $csrfHeader ? ['axenita-csrf-token' => $csrfHeader] : [] ),
                // Raw Cookie header (Laravel HTTP client doesn’t manage browser cookies automatically)
                'Cookie' => $this->cookieHeader($cookies),
            ])
            ->timeout(60);
    }

    /**
     * Generic call for any endpoint, reusable across your app.
     */
    public function request(string $method, string $uri, array $json = []): array
    {
        $res = $this->buildPendingRequest()->send($method, $uri, [
            'json' => $json,
        ]);

        // If Axenita rotates tokens/cookies and returns them in headers (some systems do),
        // parse them here and update $this->session->merge([...]).
        // Example:
        // $setCookies = $res->headers()['Set-Cookie'] ?? [];
        // $this->parseAndStoreCookies($setCookies);

        if ($res->failed()) {
            throw new RuntimeException("Axenita request failed ({$res->status()}): " . $res->body());
        }

        return $res->json() ?? [];
    }

    /**
     * Your specific endpoint wrapper (patient search).
     */
    public function searchLocalPatients(
        int $pageSize = 100,
        int $currentPage = 1,
        string $queryString = '',
        array $filters = [],
        array $sortFields = [],
        array $sortDirections = [],
    ): array {
        return $this->request('POST', '/api/contacts/patient-search/search/local-patients', [
            'paginationParam' => [
                'pageSize' => $pageSize,
                'currentPage' => $currentPage,
                'visiblePageCount' => 100,
            ],
            'queryString' => $queryString,
            'sortParam' => [
                'sortFields' => $sortFields,
                'sortDirections' => $sortDirections,
            ],
            'filterParam' => [
                'filterValueParams' => $filters,
            ],
        ]);
    }
}
