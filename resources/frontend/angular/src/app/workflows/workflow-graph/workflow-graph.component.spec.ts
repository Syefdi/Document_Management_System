import { ComponentFixture, TestBed } from '@angular/core/testing';

import { WorkflowGraphComponent } from './workflow-graph.component';

describe('WorkflowGraphComponent', () => {
  let component: WorkflowGraphComponent;
  let fixture: ComponentFixture<WorkflowGraphComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [WorkflowGraphComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(WorkflowGraphComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
