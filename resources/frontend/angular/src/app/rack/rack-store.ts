import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { Rack } from '@core/domain-classes/rack';

@Injectable({
  providedIn: 'root'
})
export class RackStore {
  private _racks$ = new BehaviorSubject<Rack[]>([]);
  private _selectedRack$ = new BehaviorSubject<Rack | null>(null);
  private _loading$ = new BehaviorSubject<boolean>(false);

  public readonly racks$: Observable<Rack[]> = this._racks$.asObservable();
  public readonly selectedRack$: Observable<Rack | null> = this._selectedRack$.asObservable();
  public readonly loading$: Observable<boolean> = this._loading$.asObservable();

  get racks(): Rack[] {
    return this._racks$.value;
  }

  get selectedRack(): Rack | null {
    return this._selectedRack$.value;
  }

  get loading(): boolean {
    return this._loading$.value;
  }

  setRacks(racks: Rack[]): void {
    this._racks$.next(racks);
  }

  setSelectedRack(rack: Rack | null): void {
    this._selectedRack$.next(rack);
  }

  setLoading(loading: boolean): void {
    this._loading$.next(loading);
  }

  addRack(rack: Rack): void {
    const currentRacks = this._racks$.value;
    this._racks$.next([...currentRacks, rack]);
  }

  updateRack(updatedRack: Rack): void {
    const currentRacks = this._racks$.value;
    const index = currentRacks.findIndex(rack => rack.id === updatedRack.id);
    if (index !== -1) {
      const updatedRacks = [...currentRacks];
      updatedRacks[index] = updatedRack;
      this._racks$.next(updatedRacks);
    }
  }

  removeRack(id: string): void {
    const currentRacks = this._racks$.value;
    const filteredRacks = currentRacks.filter(rack => rack.id !== id);
    this._racks$.next(filteredRacks);
  }

  clearStore(): void {
    this._racks$.next([]);
    this._selectedRack$.next(null);
    this._loading$.next(false);
  }
}