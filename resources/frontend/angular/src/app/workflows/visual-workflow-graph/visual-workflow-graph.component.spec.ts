import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VisualWorkflowGraphComponent } from './visual-workflow-graph.component';

describe('VisualWorkflowGraphComponent', () => {
  let component: VisualWorkflowGraphComponent;
  let fixture: ComponentFixture<VisualWorkflowGraphComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [VisualWorkflowGraphComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(VisualWorkflowGraphComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
