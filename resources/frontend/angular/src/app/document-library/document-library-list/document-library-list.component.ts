import { HttpResponse } from '@angular/common/http';
import { AfterViewInit, Component, ElementRef, inject, OnInit, ViewChild } from '@angular/core';
import { MatSort } from '@angular/material/sort';
import { ResponseHeader } from '@core/domain-classes/document-header';
import { DocumentInfo } from '@core/domain-classes/document-info';
import { DocumentResource } from '@core/domain-classes/document-resource';
import { OverlayPanel } from '@shared/overlay-panel/overlay-panel.service';
import { fromEvent, merge } from 'rxjs';
import { debounceTime, distinctUntilChanged, tap } from 'rxjs/operators';
import { BaseComponent } from 'src/app/base.component';
import { DocumentLibraryService } from '../document-library.service';
import { DocumentLibraryDataSource } from './document-library-datasource';
import { SelectionModel } from '@angular/cdk/collections';
import { BasePreviewComponent } from '@shared/base-preview/base-preview.component';
import { DocumentView } from '@core/domain-classes/document-view';
import { DocumentReminderComponent } from '../document-reminder/document-reminder.component';
import { AddDocumentComponent } from '../add-document/add-document.component';
import { ReminderListComponent } from '../reminder-list/reminder-list.component';
import { DocumentCommentComponent } from 'src/app/document/document-comment/document-comment.component';
import { ClonerService } from '@core/services/clone.service';
import { CommonService, Location, Rack } from '@core/services/common.service';
import { DocumentVersion } from '@core/domain-classes/documentVersion';
import { DocumentVersionHistoryComponent } from 'src/app/document/document-version-history/document-version-history.component';
import { DocumentService } from 'src/app/document/document.service';
import { DocumentAuditTrail } from '@core/domain-classes/document-audit-trail';
import { DocumentOperation } from '@core/domain-classes/document-operation';
import { TranslationService } from '@core/services/translation.service';
import { ToastrService } from 'ngx-toastr';
import { MatDialog } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { DocumentEditComponent } from 'src/app/document/document-edit/document-edit.component';
import { DocumentCategory } from '@core/domain-classes/document-category';
import { DocumentPermissionListComponent } from 'src/app/document/document-permission/document-permission-list/document-permission-list.component';
import { DocumentUploadNewVersionComponent } from 'src/app/document/document-upload-new-version/document-upload-new-version.component';
import { SendEmailComponent } from 'src/app/document/send-email/send-email.component';
import { CommonDialogService } from '@core/common-dialog/common-dialog.service';
import { Direction } from '@angular/cdk/bidi';
import { DocumentDeleteDialogComponent } from 'src/app/document-delete-dialog/document-delete-dialog.component';
import { DocumentShareableLink } from '@core/domain-classes/document-shareable-link';
import { SharableLinkComponent } from 'src/app/document/sharable-link/sharable-link.component';
import { ClientStore } from 'src/app/client/client-store';
import { DocumentStatusStore } from 'src/app/document-status/store/document-status.store';
import { CategoryStore } from 'src/app/category/store/category-store';
import { DocumentWorkflow } from '@core/domain-classes/document-workflow';
import { DocumentWorkflowDialogComponent } from 'src/app/document/document-workflow-dialog/document-workflow-dialog.component';
import { VisualWorkflowInstance } from '@core/domain-classes/visual-workflow-instance';
import { VisualWorkflowGraphComponent } from 'src/app/workflows/visual-workflow-graph/visual-workflow-graph.component';
import { DocumentWorkflowService } from 'src/app/workflows/manage-workflow/document-workflow.service';

@Component({
  selector: 'app-document-library-list',
  templateUrl: './document-library-list.component.html',
  styleUrls: ['./document-library-list.component.scss'],
})
export class DocumentLibraryListComponent extends BaseComponent implements OnInit, AfterViewInit {
  dataSource: DocumentLibraryDataSource;
  documents: DocumentInfo[] = [];
  displayedColumns: string[] = [
    'action',
    'name',
    'statusName',
    'categoryName',
    'createdDate',
    'workflowName',
    'workflowStatus',
    'locationName',
    'rackName',
    'companyName',
    'expiredDate',
    'createdBy',
  ];
  footerToDisplayed = ['footer'];
  documentResource: DocumentResource;
  @ViewChild(MatPaginator) paginator: MatPaginator;
  @ViewChild(MatSort) sort: MatSort;
  @ViewChild('input') input: ElementRef;
  @ViewChild('metatag') metatag: ElementRef;
  selection = new SelectionModel<DocumentInfo>(true, []);
  direction: Direction;
  // documentStatusStore = inject(DocumentStatusStore);
  categoryStore = inject(CategoryStore);
  public clientStore = inject(ClientStore);
  filteredDocuments: DocumentInfo[] = [];
  sourceDocuments: DocumentInfo[] = [];
  currentStatusFilter = '';
  statusFilterOptions: string[] = [
    'Draft',
    'InProgress',
    'Rejected',
    'Completed (Workflow)',
    'Completed (Shared)'
  ];

  constructor(
    private documentLibraryService: DocumentLibraryService,
    public overlay: OverlayPanel,
    public clonerService: ClonerService,
    private documentService: DocumentService,
    private translationService: TranslationService,
    private commonService: CommonService,
    private toastrService: ToastrService,
    private dialog: MatDialog,
    private commonDialogService: CommonDialogService,
    private documentWorkflowService: DocumentWorkflowService
  ) {
    super();
    this.documentResource = new DocumentResource();
    this.documentResource.pageSize = 10;
    this.documentResource.orderBy = 'createdDate desc';
  }

  ngOnInit(): void {
    this.dataSource = new DocumentLibraryDataSource(
      this.documentLibraryService
    );

    this.dataSource.loadDocuments(this.documentResource);
    this.loadDropdownData();
    this.getResourceParameter();
    this.getLangDir();
  }

  getLangDir() {
    this.sub$.sink = this.translationService.lanDir$.subscribe(
      (c: Direction) => (this.direction = c)
    );
  }

  ngAfterViewInit() {
    this.sub$.sink = this.sort.sortChange.subscribe(
      () => (this.paginator.pageIndex = 0)
    );

    this.sub$.sink = merge(this.sort.sortChange, this.paginator.page)
      .pipe(
        tap(() => {
          this.documentResource.skip =
            this.paginator.pageIndex * this.paginator.pageSize;
          this.documentResource.pageSize = this.paginator.pageSize;
          this.documentResource.orderBy =
            this.sort.active + ' ' + this.sort.direction;
          this.dataSource.loadDocuments(this.documentResource);
    this.loadDropdownData();
        })
      )
      .subscribe();

    this.sub$.sink = fromEvent(this.input.nativeElement, 'keyup')
      .pipe(
        debounceTime(1000),
        distinctUntilChanged(),
        tap(() => {
          this.paginator.pageIndex = 0;
          this.documentResource.skip = 0;
          this.documentResource.name = this.input.nativeElement.value;
          this.dataSource.loadDocuments(this.documentResource);
    this.loadDropdownData();
        })
      )
      .subscribe();

    this.sub$.sink = fromEvent(this.metatag.nativeElement, 'keyup')
      .pipe(
        debounceTime(1000),
        distinctUntilChanged(),
        tap(() => {
          this.paginator.pageIndex = 0;
          this.documentResource.skip = 0;
          this.documentResource.metaTags = this.metatag.nativeElement.value;
          this.dataSource.loadDocuments(this.documentResource);
    this.loadDropdownData();
        })
      )
      .subscribe();
  }

  /**
   * Memeriksa apakah nama dokumen bisa diklik.
   * Akses diberikan jika status workflow adalah 'Approved' ATAU jika tidak ada workflow.
   * @param document Objek dokumen dari baris tabel.
   * @returns boolean
   */
  isDocumentAccessible(document: DocumentInfo): boolean {
    // Pastikan string 'Approved' cocok dengan data dari server
    const isApproved = document.workflowStatus === 'Approved';

    // Dokumen dianggap tidak punya workflow jika properti workflowName-nya kosong
    const hasNoWorkflow = !document.workflowName;

    return isApproved || hasNoWorkflow;
  }

  onCategoryChange(filtervalue: string) {
    if (filtervalue) {
      this.documentResource.categoryId = filtervalue;
    } else {
      this.documentResource.categoryId = '';
    }
    this.documentResource.skip = 0;
    this.paginator.pageIndex = 0;
    this.dataSource.loadDocuments(this.documentResource);
    this.loadDropdownData();
  }

  onStorageChange(filtervalue: string) {
    if (filtervalue) {
      this.documentResource.location = filtervalue;
    } else {
      this.documentResource.location = '';
    }
    this.documentResource.skip = 0;
    this.paginator.pageIndex = 0;
    this.dataSource.loadDocuments(this.documentResource);
    this.loadDropdownData();
  }

  onClientChange(filterValue: string) {
    if (filterValue) {
      this.documentResource.clientId = filterValue;
    } else {
      this.documentResource.clientId = '';
    }
    this.documentResource.skip = 0;
    this.paginator.pageIndex = 0;
    this.dataSource.loadDocuments(this.documentResource);
    this.loadDropdownData();
  }

  onDocumentStatusChange(selectedStatus: string): void {
    this.documentResource.status = selectedStatus || null;
    this.documentResource.skip = 0;
    this.paginator.pageIndex = 0;
    this.dataSource.loadDocuments(this.documentResource);
    this.loadDropdownData();
  }

  applyFilter(filterValue: string): void {
    if (filterValue) {
      // Saring data asli (sourceDocuments) berdasarkan status
      this.filteredDocuments = this.sourceDocuments.filter(doc => {
        const status = this.getDocumentStatus(doc).text;
        return status === filterValue;
      });
    } else {
      // Jika filter kosong, tampilkan semua data dari halaman saat ini
      this.filteredDocuments = [...this.sourceDocuments];
    }
  }

  getResourceParameter() {
    this.sub$.sink = this.dataSource.responseHeaderSubject$.subscribe(
      (c: ResponseHeader) => {
        if (c) {
          this.documentResource.pageSize = c.pageSize;
          this.documentResource.skip = c.skip;
          this.documentResource.totalCount = c.totalCount;
        }
      }
    );
  }

  getDocuments(): void {
    this.sub$.sink = this.documentLibraryService
      .getDocuments(this.documentResource)
      .subscribe(
        (resp: HttpResponse<DocumentInfo[]>) => {
          const paginationParam = JSON.parse(
            resp.headers.get('X-Pagination')
          ) as ResponseHeader;
          this.documentResource.pageSize = paginationParam.pageSize;
          this.documentResource.skip = paginationParam.skip;
          this.documents = [...resp.body];
        },
      );
  }

  getDocumentStatus(document: DocumentInfo): { text: string, color: string } {
      const createdById = document.createdBy; // string UUID
      const userPermissions = (document.documentUserPermissions || []).filter(
        p => p.userId !== createdById
      );
      const hasUser = userPermissions.length > 0;
      const hasRole = Array.isArray(document.documentRolePermissions) && document.documentRolePermissions.length > 0;
      const workflow = document.documentWorkflow;
      const hasPermission = hasUser || hasRole;

      let statusText = 'Unknown';
      let color = 'black';

      if (!workflow && !hasPermission) {
        statusText = 'Draft';
        color = 'gray';
      } else if (workflow?.status === 'Completed') {
        statusText = 'Completed';
        color = 'green';
      } else if (workflow?.status === 'Cancelled') {
        statusText = 'Rejected';
        color = 'red';
      } else if (workflow) {
        statusText = 'InProgress';
        color = 'blue';
      } else if (!workflow && hasPermission) {
        statusText = 'Completed';
        color = 'green';
      }

      return { text: statusText, color };
    }





  getExpiryDate(
    maxRolePermissionEndDate: Date,
    maxUserPermissionEndDate: Date
  ) {
    if (maxRolePermissionEndDate && maxUserPermissionEndDate) {
      return maxRolePermissionEndDate > maxUserPermissionEndDate
        ? maxRolePermissionEndDate
        : maxUserPermissionEndDate;
    } else if (maxRolePermissionEndDate) {
      return maxRolePermissionEndDate;
    } else if (maxUserPermissionEndDate) {
      return maxUserPermissionEndDate;
    } else {
      return null;
    }
  }

  addReminder(documentInfo: DocumentInfo) {
    this.dialog.open(DocumentReminderComponent, {
      data: documentInfo,
      width: '80vw',
      maxHeight: '90vh',
    });
  }

  onReminderList() {
    this.dialog.open(ReminderListComponent, {
      data: null,
      width: '80vw',
      maxHeight: '80vh',
    });
  }

  onAddDocument() {
    const dialogRef = this.dialog.open(AddDocumentComponent, {
      data: null,
      width: '80vw',
      maxHeight: '80vh',
    });

    this.sub$.sink = dialogRef.afterClosed().subscribe((result: boolean) => {
      if (result) {
        this.dataSource.loadDocuments(this.documentResource);
    this.loadDropdownData();
      }
    });
  }

  onDocumentView(document: DocumentInfo) {
    const urls = document.url.split('.');
    const extension = urls[1];
    const documentView: DocumentView = {
      documentId: document.id,
      name: document.name,
      extension: extension,
      isVersion: false,
      isFromPublicPreview: false,
      isPreviewDownloadEnabled: false,
      isFromFileRequest: false,
    };
    this.overlay.open(BasePreviewComponent, {
      position: 'center',
      origin: 'global',
      panelClass: ['file-preview-overlay-container', 'white-background'],
      data: documentView,
    });
  }

  addDocumentTrail(id: string, operation: string) {
    const objDocumentAuditTrail: DocumentAuditTrail = {
      documentId: id,
      operationName: operation,
    };
    this.sub$.sink = this.commonService
      .addDocumentAuditTrail(objDocumentAuditTrail)
      // eslint-disable-next-line @typescript-eslint/no-empty-function
      .subscribe(() => { });
  }

  addComment(document: DocumentInfo) {
    const dialogRef = this.dialog.open(DocumentCommentComponent, {
      width: '800px',
      maxHeight: '70vh',
      data: Object.assign({}, document),
    });

    this.sub$.sink = dialogRef.afterClosed().subscribe((isCommentChanged: boolean) => {
      if (isCommentChanged) {
        this.dataSource.loadDocuments(this.documentResource);
    this.loadDropdownData();
      }
    });
  }

  editDocument(documentInfo: DocumentInfo) {
    const documentCategories: DocumentCategory = {
      document: documentInfo,
      categories: this.categoryStore.categories(),
      clients: this.clientStore.clients(),
    };
    const dialogRef = this.dialog.open(DocumentEditComponent, {
      width: '600px',
      data: Object.assign({}, documentCategories),
    });

    this.sub$.sink = dialogRef.afterClosed().subscribe((result: string) => {
      if (result === 'loaded') {
        this.dataSource.loadDocuments(this.documentResource);
    this.loadDropdownData();
      }
    });
  }

  onVersionHistoryClick(document: DocumentInfo): void {
    const documentInfo = this.clonerService.deepClone<DocumentInfo>(document);
    this.sub$.sink = this.documentService
      .getDocumentVersion(document.id)
      .subscribe((documentVersions: DocumentVersion[]) => {
        documentInfo.documentVersions = documentVersions;
        this.dialog.open(DocumentVersionHistoryComponent, {
          width: '800px',
          maxHeight: '70vh',
          panelClass: 'full-width-dialog',
          data: Object.assign({}, documentInfo),
        });
      });
  }

  manageDocumentPermission(documentInfo: DocumentInfo) {
    this.dialog.open(DocumentPermissionListComponent, {
      data: documentInfo,
      width: '80vw',
      maxHeight: '80vh',
    });
  }

  uploadNewVersion(document: DocumentInfo) {
    const dialogRef = this.dialog.open(DocumentUploadNewVersionComponent, {
      width: '800px',
      maxHeight: '70vh',
      data: Object.assign({}, document),
    });

    this.sub$.sink = dialogRef.afterClosed().subscribe((result: boolean) => {
      if (result) {
        this.dataSource.loadDocuments(this.documentResource);
    this.loadDropdownData();
      }
    });
  }

  sendEmail(documentInfo: DocumentInfo) {
    this.dialog.open(SendEmailComponent, {
      data: documentInfo,
      width: '80vw',
      maxHeight: '80vh',
    });
  }

  archiveDocument(document: DocumentInfo) {
    this.sub$.sink = this.commonDialogService
      .deleteConformationDialog(
        this.translationService.getValue('ARE_YOU_SURE_YOU_WANT_TO_ARCHIVE'),
        document.name
      )
      .subscribe((isTrue: boolean) => {
        if (isTrue) {
          this.sub$.sink = this.documentService
            .archiveDocument(document.id)
            .subscribe(() => {
              this.addDocumentTrail(
                document.id,
                DocumentOperation.Archived.toString()
              );
              this.toastrService.success(
                this.translationService.getValue(
                  'DOCUMENT_ARCHIVED_SUCCESSFULLY'
                )
              );
              this.dataSource.loadDocuments(this.documentResource);
    this.loadDropdownData();
            });
        }
      });
  }

  deleteDocument(document: DocumentInfo) {
    const dialogRef = this.dialog.open(DocumentDeleteDialogComponent, {
      width: '50vw',
      maxHeight: '70vh',
    });

    dialogRef.afterClosed().subscribe((isTrue: boolean) => {
      if (isTrue) {
        this.sub$.sink = this.documentService
          .deleteDocument(document.id)
          .subscribe(() => {
            this.addDocumentTrail(
              document.id,
              DocumentOperation.Deleted.toString()
            );
            this.toastrService.success(
              this.translationService.getValue('DOCUMENT_DELETED_SUCCESSFULLY')
            );
            this.dataSource.loadDocuments(this.documentResource);
    this.loadDropdownData();
          });
      }
    });
  }

  onCreateShareableLink(document: DocumentInfo) {
    this.sub$.sink = this.documentService
      .getDocumentShareableLink(document.id)
      .subscribe((link: DocumentShareableLink) => {
        this.dialog.open(SharableLinkComponent, {
          width: '500px',
          data: { document, link },
        });
      });
  }

  manageWorkflowInstance(documentInfo: DocumentInfo) {
    const document = {
      document: documentInfo,
    };
    const dialogRef = this.dialog.open(DocumentWorkflowDialogComponent, {
      width: '40vw',
      maxHeight: '70vh',
      data: Object.assign({}, document),
    });

    this.sub$.sink = dialogRef
      .afterClosed()
      .subscribe((result: DocumentWorkflow) => {
        if (result && result?.workflowId) {
          this.dataSource.loadDocuments(this.documentResource);
    this.loadDropdownData();
        }
      });
  }

  // checkWorkflowInstance(documentInfo: DocumentInfo) {
  //   if (documentInfo?.isWorkflowCompleted) {
  //     return true;
  //   }
  //   return false;
  // }

  viewVisualWorkflow(workflowInstance: DocumentInfo): void {
    this.documentWorkflowService.getRunningVisualWorkflow(workflowInstance.documentWorkflowId)
      .subscribe({
        next: (data: VisualWorkflowInstance) => {
          data.documentId = workflowInstance.id;
          data.documentName = workflowInstance.name;
          (data as any).workflowStatus = workflowInstance.workflowStatus;
          this.dialog.open(VisualWorkflowGraphComponent, {
            minWidth: '90vw',
            data: Object.assign({}, data),
          });
        },
        error: (error) => { },
      });
  }
  locations: Location[] = [];
  racks: Rack[] = [];

      loadDropdownData(): void {
    // Load locations data
    this.commonService.getLocations().subscribe({
      next: (locations) => {
        console.log('Locations response:', locations);
        if (Array.isArray(locations)) {
          this.locations = locations;
          console.log('Locations loaded:', this.locations.length, 'items');
        }
      },
      error: (error) => {
        console.error('Error loading locations:', error);
      }
    });

    // Load racks data
    this.commonService.getRacks().subscribe({
      next: (racks) => {
        if (Array.isArray(racks)) {
          this.racks = racks;
        }
      },
      error: (error) => {
        console.error('Error loading racks:', error);
      }
    });
  }
}













