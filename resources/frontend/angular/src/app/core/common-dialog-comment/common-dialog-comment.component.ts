// import { Component, OnInit } from '@angular/core';
import { UntypedFormBuilder, UntypedFormGroup, Validators } from '@angular/forms';
// import { MatDialogRef } from '@angular/material/dialog';
import { BaseComponent } from 'src/app/base.component';
import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { DocumentWorkflowService } from 'src/app/workflows/manage-workflow/document-workflow.service'; // sesuaikan path
import { ToastrService } from 'ngx-toastr';


@Component({
  selector: 'app-common-dialog-comment',
  templateUrl: './common-dialog-comment.component.html',
  styleUrl: './common-dialog-comment.component.scss'
})
export class CommonDialogCommentComponent extends BaseComponent {
  primaryMessage: string;
  note: string = '';
  commentForm: UntypedFormGroup;

  constructor(public dialogRef: MatDialogRef<CommonDialogCommentComponent>,
    private fb: UntypedFormBuilder,
    private toastrService: ToastrService,
     private documentWorkflowService: DocumentWorkflowService,
    @Inject(MAT_DIALOG_DATA) public data: { documentWorkflowId: string }
  ) { super(); }

  ngOnInit(): void {
    this.createForm();
  }

  createForm() {
    this.commentForm = this.fb.group({
      comment: ['', [Validators.required]],
    });
  }

  onCancel(): void {
    this.dialogRef.close();
  }

  clickHandler(flag: boolean): void {
    if (this.commentForm.invalid) {
      this.commentForm.markAllAsTouched();
      return;
    }
    if (flag) {
      const comment = this.commentForm.get('comment').value;
      this.dialogRef.close({
        flag: flag,
        comment: comment
      });
    }
    else {
      this.dialogRef.close(false);
    }
  }

  clickRejected() {
    if (this.commentForm.invalid) {
      this.commentForm.markAllAsTouched();
      return;
    }

    const comment = this.commentForm.get('comment')?.value;
    if (!this.data?.documentWorkflowId) {
      console.error('documentWorkflowId is missing!');
      return;
    }
    // misal kamu punya service untuk update status
    this.documentWorkflowService.cancelWOrkflow(this.data.documentWorkflowId, comment)
        .subscribe({
          next: () => {
            this.toastrService.success('The workflow was successfully Rejected'); // ✅ Tambahkan ini
            this.dialogRef.close({ status: 'Cancelled', comment });
          },
          error: err => console.error(err)
        });
  }
}
