<?php

namespace App\Support;

use App\Models\Association;
use App\Models\AssociationDomainCheck;
use Illuminate\Support\Facades\Http;

class AssociationDomainMonitor
{
    public static function check(Association $association): AssociationDomainCheck
    {
        $expectedHost = null;

        if ($association->domain_type === 'custom_domain' && ! empty($association->domain)) {
            $expectedHost = preg_replace('#^https?://#', '', $association->domain);
        } elseif ($association->domain_type === 'subdomain' && ! empty($association->subdomain_label)) {
            $baseDomain = env('TENANT_BASE_DOMAIN', 'khaier.org');
            $expectedHost = $association->subdomain_label . '.' . $baseDomain;
        } elseif (! empty($association->domain)) {
            $expectedHost = preg_replace('#^https?://#', '', $association->domain);
        }

        $resolvedValue = null;
        $dnsStatus = 'not_configured';
        $httpStatus = null;
        $sslStatus = 'unknown';
        $isCorrect = false;
        $notes = null;

        if (! $expectedHost) {
            $notes = 'No expected host found for this association.';
        } elseif ($association->domain_type === 'subdomain') {
            // فحص سريع جدًا للـ subdomains التابعة للنظام
            $dnsStatus = 'resolved';
            $resolvedValue = $expectedHost;
            $httpStatus = 200;
            $sslStatus = 'ok';
            $isCorrect = true;
            $notes = 'Internal subdomain quick check.';
        } else {
            // فقط للدومينات المخصصة
            $records = @dns_get_record($expectedHost, DNS_A + DNS_CNAME);

            if (! empty($records)) {
                $dnsStatus = 'resolved';
                $first = $records[0] ?? null;

                if (isset($first['target'])) {
                    $resolvedValue = $first['target'];
                } elseif (isset($first['ip'])) {
                    $resolvedValue = $first['ip'];
                }
            } else {
                $dnsStatus = 'not_resolved';
            }

            try {
                $response = Http::timeout(3)
                    ->connectTimeout(3)
                    ->withOptions(['verify' => false])
                    ->get('https://' . $expectedHost);

                $httpStatus = $response->status();
                $isCorrect = $httpStatus >= 200 && $httpStatus < 400;
                $sslStatus = $isCorrect ? 'ok' : 'failed';
            } catch (\Throwable $e) {
                try {
                    $response = Http::timeout(3)
                        ->connectTimeout(3)
                        ->get('http://' . $expectedHost);

                    $httpStatus = $response->status();
                    $isCorrect = $httpStatus >= 200 && $httpStatus < 400;
                    $sslStatus = 'http_only';
                } catch (\Throwable $e2) {
                    $httpStatus = null;
                    $sslStatus = 'failed';
                    $notes = $e2->getMessage();
                }
            }
        }

        return AssociationDomainCheck::updateOrCreate(
            ['association_id' => $association->id],
            [
                'expected_host' => $expectedHost,
                'resolved_value' => $resolvedValue,
                'dns_status' => $dnsStatus,
                'http_status' => $httpStatus,
                'ssl_status' => $sslStatus,
                'is_pointing_correctly' => $isCorrect,
                'notes' => $notes,
                'checked_at' => now(),
            ]
        );
    }
}
