import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ManageStepComponent } from './manage-step.component';

describe('ManageStepComponent', () => {
  let component: ManageStepComponent;
  let fixture: ComponentFixture<ManageStepComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ManageStepComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ManageStepComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
