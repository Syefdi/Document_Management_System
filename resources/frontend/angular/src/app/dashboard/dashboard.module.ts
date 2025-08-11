import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DashboardRoutingModule } from './dashboard-routing.module';
import { DashboardComponent } from './dashboard.component';
import { MatSortModule } from '@angular/material/sort';
import { SharedModule } from '@shared/shared.module';
import { DocumentByCategoryChartComponent } from './document-by-category-chart/document-by-category-chart.component';
import { NgxChartsModule } from '@swimlane/ngx-charts';
import { CalendarModule, DateAdapter } from 'angular-calendar';
import { adapterFactory } from 'angular-calendar/date-adapters/date-fns';
import { CalenderViewComponent } from './calender-view/calender-view.component';
import { MatPaginatorModule } from '@angular/material/paginator';
import { MatTableModule } from '@angular/material/table';
import { MatCardModule } from '@angular/material/card';
import { MyWorkflowComponent } from './my-workflow/my-workflow.component';
import { MatTooltipModule } from '@angular/material/tooltip';

@NgModule({
  declarations: [
    DashboardComponent,
    DocumentByCategoryChartComponent,
    CalenderViewComponent,
    MyWorkflowComponent
  ],
  imports: [
    CommonModule,
    DashboardRoutingModule,
    MatSortModule,
    MatPaginatorModule,
    MatTableModule,
    MatCardModule,
    SharedModule,
    NgxChartsModule,
    MatTooltipModule,
    CalendarModule.forRoot({
      provide: DateAdapter,
      useFactory: adapterFactory,
    }),
  ],
})
export class DashboardModule { }
