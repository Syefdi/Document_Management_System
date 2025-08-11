import { User } from './user';

export class UserAuth {
  isAuthenticated = false;
  authorisation: Authorisation;
  user: User;
  claims: string[] = [];
  status: string;
  tokenTime: Date;
}

export class Authorisation {
  token: string;
  type: string;
}

export class AuthToken {
  email: string;
  userId: string;
  claims: string[] = [];
  iat: number;
}
