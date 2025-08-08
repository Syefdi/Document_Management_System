import { Category } from './category';
import { Client } from './client';
import { DocumentInfo } from './document-info';
import { DocumentStatus } from './document-status';

export interface DocumentCategory {
  document: DocumentInfo;
  categories: Category[];
  clients: Client[];
  documentStatuses?: DocumentStatus[];
  isCategoryReadonly?: boolean;
}
