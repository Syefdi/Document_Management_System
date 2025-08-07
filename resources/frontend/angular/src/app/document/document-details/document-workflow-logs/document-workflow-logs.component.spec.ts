import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DocumentWorkflowLogsComponent } from './document-workflow-logs.component';

describe('DocumentWorkflowLogsComponent', () => {
  let component: DocumentWorkflowLogsComponent;
  let fixture: ComponentFixture<DocumentWorkflowLogsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [DocumentWorkflowLogsComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DocumentWorkflowLogsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
