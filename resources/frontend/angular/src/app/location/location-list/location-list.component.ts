import { CommonModule } from '@angular/common';
import { Component, inject, OnInit } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatIconModule } from '@angular/material/icon';
import { MatInputModule } from '@angular/material/input';
import { MatTableModule } from '@angular/material/table';
import { RouterModule } from '@angular/router';
import { CommonDialogService } from '@core/common-dialog/common-dialog.service';
import { TranslationService } from '@core/services/translation.service';
import { TranslateModule } from '@ngx-translate/core';
import { SharedModule } from '@shared/shared.module';
import { FeatherModule } from 'angular-feather';
import { BaseComponent } from 'src/app/base.component';
import { LocationStore } from '../location-store';
import { Location } from '@core/domain-classes/location';

@Component({
  selector: 'app-location-list',
  standalone: true,
  imports: [FormsModule,
      TranslateModule,
      CommonModule,
      RouterModule,
      MatButtonModule,
      ReactiveFormsModule,
      FeatherModule,
      MatIconModule,
      MatCardModule,
      SharedModule,
      MatFormFieldModule,
      MatTableModule,
      MatInputModule],
  templateUrl: './location-list.component.html',
  styleUrl: './location-list.component.scss'
})
export class LocationListComponent extends BaseComponent implements OnInit {

  locations: Location[] = [];
  displayedColumns: string[] = ['action', 'name', 'description', 'address'];

  public locationStore = inject(LocationStore);
  private commonDialogService = inject(CommonDialogService);
  private translationService = inject(TranslationService);

  ngOnInit(): void {
    this.locationStore.loadLocations();
  }

  deleteLocation(location: Location) {
    this.sub$.sink = this.commonDialogService
      .deleteConformationDialog(`${this.translationService.getValue('ARE_YOU_SURE_YOU_WANT_TO_DELETE')} ${location.name}`)
      .subscribe((isTrue: boolean) => {
        if (isTrue) {
          this.locationStore.deleteLocationById(location.id);
        }
      });
  }

  refresh() {
    this.locationStore.loadLocations();
  }
}