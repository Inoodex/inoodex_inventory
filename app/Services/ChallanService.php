<?php

namespace App\Services;

use App\Models\Challan;
use App\Models\CompanyDetail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class ChallanService
{
    /**
     * Generate binary PDF content and suggested filename for a given Challan.
     *
     * @param Challan $challan
     * @return array{content: string, filename: string}
     */
    public function renderPdf(Challan $challan): array
    {
        $challan->loadMissing(['challanItems', 'sale.customer', 'project.client']);

        $recipientName = $challan->recipient_organization;
        $recipientAddress = $challan->recipient_address;

        if (!$recipientName) {
            if ($challan->type === 'sale' && $challan->sale && $challan->sale->customer) {
                $recipientName = $challan->sale->customer->name;
            } elseif ($challan->type === 'project' && $challan->project && $challan->project->client) {
                $recipientName = $challan->project->client->name;
            } else {
                $recipientName = 'N/A';
            }
        }

        if (!$recipientAddress) {
            if ($challan->type === 'sale' && $challan->sale && $challan->sale->customer) {
                $recipientAddress = $challan->sale->customer->address;
            } elseif ($challan->type === 'project' && $challan->project && $challan->project->client) {
                $recipientAddress = $challan->project->client->address;
            } else {
                $recipientAddress = 'N/A';
            }
        }

        $signatoryName = $challan->signatory_name ?? 'Engr. Shamsul Alam';
        $companyDetail = CompanyDetail::where('signatory_name', $signatoryName)->first()
            ?? CompanyDetail::where('is_active', true)->first()
            ?? CompanyDetail::first();

        $pdfData = [
            'challan'                => $challan,
            'recipient_organization' => $recipientName,
            'recipient_designation'  => $challan->recipient_designation ?? 'The Managing Director',
            'recipient_address'      => $recipientAddress,
            'attention_to'           => $challan->attention_to ?? '',
            'subject'                => $challan->subject ?? 'Delivery Challan',
            'show_signature'         => $challan->show_signature ?? true,
            'show_seal'              => $challan->show_seal ?? true,
            'signature_image'        => $companyDetail?->signature_image ?? null,
            'seal_image'             => $companyDetail?->seal_image ?? null,
        ];

        ini_set('memory_limit', '512M');
        $html = view('pdf.challan', $pdfData)->render();
        $mpdf = new Mpdf([
            'mode'         => 'utf-8',
            'format'       => 'A4',
            'default_font' => 'Helvetica',
        ]);
        $mpdf->WriteHTML($html);

        $fileRecipient = $recipientName ?: 'challan';
        $recipientSlug = Str::slug($fileRecipient);
        $challanDate = $challan->challan_date
            ? Carbon::parse($challan->challan_date)->format('d-m-Y')
            : now()->format('d-m-Y');
        $fileName = ($recipientSlug ?: 'challan') . '-' . $challanDate . '.pdf';

        $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

        return [
            'content'  => $pdfContent,
            'filename' => $fileName,
        ];
    }
}
