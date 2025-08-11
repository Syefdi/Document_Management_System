import { CommonModule, NgFor, NgIf } from '@angular/common';
import { AfterViewInit, Component, Inject, ViewChild } from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { MAT_DIALOG_DATA, MatDialogModule, MatDialogRef } from '@angular/material/dialog';
import { MatIconModule } from '@angular/material/icon';
import { CustomColor } from '@core/domain-classes/custom-color';
import { DocumentInfo } from '@core/domain-classes/document-info';
import { Link } from '@core/domain-classes/link';
import { VisualWorkflowInstance } from '@core/domain-classes/visual-workflow-instance';
import { ClonerService } from '@core/services/clone.service';
import { TranslateModule } from '@ngx-translate/core';
import { BasePreviewComponent } from '@shared/base-preview/base-preview.component';
import { OverlayPanel } from '@shared/overlay-panel/overlay-panel.service';
import { PipesModule } from '@shared/pipes/pipes.module';
import { NgxGraphModule } from '@swimlane/ngx-graph';
import { FeatherModule } from 'angular-feather';
import { curveMonotoneX } from 'd3-shape';
import { DocumentService } from 'src/app/document/document.service';
import { Node } from '@swimlane/ngx-graph';

@Component({
  selector: 'app-visual-workflow-graph',
  standalone: true,
  imports: [NgxGraphModule,
    MatButtonModule,
    MatIconModule,
    MatDialogModule,
    CommonModule,
    PipesModule,
    FeatherModule,
    TranslateModule],
  providers: [NgFor, NgIf],
  templateUrl: './visual-workflow-graph.component.html',
  styleUrl: './visual-workflow-graph.component.scss'
})
export class VisualWorkflowGraphComponent implements AfterViewInit {
  nodes: Node[] = [];
  links: Link[] = [];
  curve = curveMonotoneX; // Choose your curve type
  customColors = [];
  @ViewChild('graph') graph: any;

  constructor(public dialogRef: MatDialogRef<VisualWorkflowGraphComponent>,
    @Inject(MAT_DIALOG_DATA) public data: VisualWorkflowInstance,
    private clonerService: ClonerService,
    private documentService: DocumentService,
    public overlay: OverlayPanel,) {
    this.nodes = this.clonerService.deepClone<Node[]>(data.nodes);
    this.links = this.clonerService.deepClone<Link[]>(data.links);
    this.customColors = this.clonerService.deepClone<CustomColor[]>(data.customColors);
  }

  onDocumentCancel(): void {
    this.dialogRef.close();
  }

  onDocumentView() {
    this.documentService.getDocument(this.data.documentId).subscribe((document: DocumentInfo) => {
      const urls = document.url.split('.');
      const extension = urls[1];
      const documentView = {
        documentId: document.id,
        name: document.name,
        //url: document.url,
        extension: extension,
        isVersion: false,
        isFromPublicPreview: false,
        isPreviewDownloadEnabled: false,
        // isFileRequestDocument: false,
      };
      this.overlay.open(BasePreviewComponent, {
        position: 'center',
        origin: 'global',
        panelClass: ['file-preview-overlay-container', 'white-background'],
        data: documentView,
      });
    });
  }
  ngAfterViewInit() {
    setTimeout(() => {
      if (this.graph) {
        // this.graph?.zoomToFit(); // Auto zooms to fit view
      }
    });
  }

}
