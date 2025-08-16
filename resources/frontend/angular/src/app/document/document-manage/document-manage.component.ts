import { Component } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { of } from 'rxjs';
import { switchMap, map } from 'rxjs/operators';
import { BaseComponent } from 'src/app/base.component';
import { DocumentService } from '../document.service';
import { DocumentInfo } from '@core/domain-classes/document-info';
import { Router } from '@angular/router';
import { DocumentAuditTrail } from '@core/domain-classes/document-audit-trail';
import { DocumentOperation } from '@core/domain-classes/document-operation';
import { CommonService } from '@core/services/common.service';
import { TranslationService } from '@core/services/translation.service';
// BARU: Impor untuk service dan domain class workflow
import { DocumentWorkflow } from '@core/domain-classes/document-workflow';
import { DocumentWorkflowService } from 'src/app/workflows/manage-workflow/document-workflow.service';

@Component({
  selector: 'app-document-manage',
  templateUrl: './document-manage.component.html',
  styleUrls: ['./document-manage.component.scss'],
})
export class DocumentManageComponent extends BaseComponent {
  // MODIFIKASI: Properti ini tidak lagi dibutuhkan di sini karena sudah ditangani oleh presentation component
  // documentForm: UntypedFormGroup;
  // documentSource: string;

  constructor(
    private toastrService: ToastrService,
    private documentService: DocumentService,
    private router: Router,
    private commonService: CommonService,
    private translationService: TranslationService,
    // BARU: Injeksi service workflow
    private documentWorkflowService: DocumentWorkflowService
  ) {
    super();
  }

  // MODIFIKASI TOTAL: Fungsi saveDocument sekarang menerima objek DocumentInfo dari presentation component
  // dan menjalankan semua proses secara berurutan menggunakan RxJS switchMap.
  saveDocument(document: DocumentInfo) {
    // Rangkaian proses: 1. Buat Dokumen -> 2. Mulai Workflow (jika ada) -> 3. Buat Audit Trail
    this.sub$.sink = this.documentService.addDocument(document)
      .pipe(
        switchMap((documentInfo: DocumentInfo) => {
          this.toastrService.success(this.translationService.getValue('DOCUMENT_SAVE_SUCCESSFULLY'));

          // Cek apakah ada workflow yang dipilih (workflowId dikirim dari presentation component)
          if (document.workflowId) {
            const documentWorkflow: DocumentWorkflow = {
              workflowId: document.workflowId,
              documentId: documentInfo.id,
            };
            // Mulai workflow dan teruskan documentInfo ke langkah selanjutnya
            return this.documentWorkflowService.addDocumentWorkflow(documentWorkflow).pipe(
              map(() => documentInfo)
            );
          }
          // Jika tidak ada workflow, langsung teruskan documentInfo ke langkah selanjutnya
          return of(documentInfo);
        }),
        switchMap((documentInfo: DocumentInfo) => {
          // Buat audit trail untuk dokumen yang baru dibuat
          const objDocumentAuditTrail: DocumentAuditTrail = {
            documentId: documentInfo.id,
            operationName: DocumentOperation.Created.toString(),
          };
          return this.commonService.addDocumentAuditTrail(objDocumentAuditTrail);
        })
      )
      .subscribe(() => {
        // Setelah semua proses di atas berhasil, navigasi ke halaman utama
        this.router.navigate(['/documents']);
      });
  }

  // FUNGSI addDocumentTrail TIDAK DIPERLUKAN LAGI SECARA TERPISAH
  // KARENA LOGIKANYA SUDAH DISATUKAN DI DALAM FUNGSI saveDocument
}
