import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MatTableModule } from '@angular/material/table';

import { Subject, takeUntil } from 'rxjs';
import { Rack } from '@core/domain-classes/rack';
import { RackService } from '../rack.service';
import { RackStore } from '../rack-store';
import { CommonError } from '@core/error-handler/common-error';
import { ToastrService } from 'ngx-toastr';
import { TranslateModule } from '@ngx-translate/core';
import { FeatherModule } from 'angular-feather';
import { SharedModule } from '@shared/shared.module';
import { CommonDialogService } from '@core/common-dialog/common-dialog.service';
import { TranslationService } from '@core/services/translation.service';

@Component({
  selector: 'app-rack-list',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    MatTableModule,

    TranslateModule,
    FeatherModule,
    SharedModule
  ],
  templateUrl: './rack-list.component.html',
  styleUrls: ['./rack-list.component.scss']
})
export class RackListComponent implements OnInit, OnDestroy {
  private destroy$ = new Subject<void>();
  
  displayedColumns: string[] = ['action', 'name', 'description'];
  racks: Rack[] = [];
  loading = false;

  constructor(
    private rackService: RackService,
    private rackStore: RackStore,
    private toastr: ToastrService,
    private commonDialogService: CommonDialogService,
    private translationService: TranslationService
  ) {}

  ngOnInit(): void {
    this.subscribeToStore();
    this.loadRacks();
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  private subscribeToStore(): void {
    this.rackStore.racks$
      .pipe(takeUntil(this.destroy$))
      .subscribe(racks => {
        this.racks = racks;
      });

    this.rackStore.loading$
      .pipe(takeUntil(this.destroy$))
      .subscribe(loading => {
        this.loading = loading;
      });
  }

  private loadRacks(): void {
    this.rackStore.setLoading(true);
    this.rackService.getRacks()
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (result) => {
          if (result && typeof result === 'object' && 'message' in result) {
            this.toastr.error((result as CommonError).messages?.join(', ') || 'An error occurred', 'Error');
          } else {
            this.rackStore.setRacks(result as Rack[]);
          }
          this.rackStore.setLoading(false);
        },
        error: (error) => {
          this.toastr.error('Failed to load racks', 'Error');
          this.rackStore.setLoading(false);
        }
      });
  }

  deleteRack(rack: Rack): void {
    this.commonDialogService
      .deleteConformationDialog(
        this.translationService.getValue('ARE_YOU_SURE_YOU_WANT_TO_DELETE'),
        rack.name
      )
      .subscribe((isTrue: boolean) => {
        if (isTrue) {
          this.rackService.deleteRack(rack.id!)
            .pipe(takeUntil(this.destroy$))
            .subscribe({
              next: (result) => {
                // Check if result is an error object
                if (result && typeof result === 'object' && 'messages' in result) {
                  this.toastr.error((result as CommonError).messages?.join(', ') || 'An error occurred', 'Error');
                } else {
                  // Success case - result should be void or success response
                  this.rackStore.removeRack(rack.id!);
                  this.toastr.success(
                    this.translationService.getValue('RACK_DELETED_SUCCESSFULLY') || 'Rack deleted successfully',
                    this.translationService.getValue('SUCCESS') || 'Success'
                  );
                }
              },
              error: (error) => {
                // Handle HTTP errors
                if (error && error.error && error.error.messages) {
                  this.toastr.error(error.error.messages.join(', '), 'Error');
                } else {
                  this.toastr.error(
                    this.translationService.getValue('FAILED_TO_DELETE_RACK') || 'Failed to delete rack',
                    this.translationService.getValue('ERROR') || 'Error'
                  );
                }
              }
            });
        }
      });
  }



  refresh(): void {
    this.loadRacks();
  }
}