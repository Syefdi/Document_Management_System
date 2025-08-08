import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MyWorkflowComponent } from './my-workflow.component';

describe('MyWorkflowComponent', () => {
  let component: MyWorkflowComponent;
  let fixture: ComponentFixture<MyWorkflowComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [MyWorkflowComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(MyWorkflowComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
