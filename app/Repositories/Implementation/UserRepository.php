<?php

namespace App\Repositories\Implementation;

use App\Models\EmailSMTPSettings;
use App\Models\UserRoles;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Implementation\BaseRepository;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\EmailRepositoryInterface;
use Illuminate\Support\Facades\Hash;

//use Your Model

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor..
     *
     *
     * @param Model $model
     */

    public $emailRepository;

    function __construct(EmailRepositoryInterface $emailRepository)
    {
        parent::__construct();
        $this->emailRepository = $emailRepository;
    }

    public static function model()
    {
        return Users::class;
    }

    public function createUser(array $attributes)
    {
        try {
            DB::beginTransaction();
            $model = $this->model->newInstance($attributes);
            $model->save();
            $this->resetModel();
            $result = $this->parseResult($model);
            foreach ($attributes['roleIds'] as $roleId) {
                $model = UserRoles::create(array(
                    'userId' =>   $result->id,
                    'roleId' =>  $roleId,
                ));
            }
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error in saving data.',
            ], 409);
        }
    }

    public function getUsersForDropdown()
    {
        $users = Users::select(['id', 'firstName', 'lastName', 'userName', 'email'])->get();
        return $users;
    }

    public function findUser($id)
    {
        $model = $this->model->with('userRoles')->with('userClaims')->findOrFail($id);
        $this->resetModel();
        return $this->parseResult($model);
    }

    public function updateUser($model, $id, $userRoles)
    {
        try {
            DB::beginTransaction();
            $userRoles1 =  UserRoles::where('userId', '=', $id)->get('id');
            UserRoles::destroy($userRoles1);
            $result = $this->parseResult($model);

            foreach ($userRoles as $roleId) {
                UserRoles::create(array(
                    'userId' =>   $result->id,
                    'roleId' =>  $roleId,
                ));
            }

            $model->save();

            $this->resetModel();

            $result = $this->parseResult($model);
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error in saving data.',
            ], 409);
        }
    }



    public function updateUserProfile($request)
    {
        try {
            $userId = Auth::parseToken()->getPayload()->get('userId');
            if ($userId == null) {
                return response()->json([
                    'message' => 'User does not exist.',
                ], 404);
            }

            $model = $this->model->findOrFail($userId);

            $model->firstName = $request->firstName;
            $model->lastName = $request->lastName;
            $model->phoneNumber = $request->phoneNumber;
            $model->save();
            return [];
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in saving data.',
            ], 409);
        }
    }

    public function forgotpassword($request)
    {

        $appuser = Users::where('email', '=', $request->email)->first();

        if ($appuser == null) {
            return response()->json([
                'Message' => 'User does not exists.',
            ], 409);
        }

        $defaultEmailSMTPSettings = EmailSMTPSettings::where('isDefault', '=', 1)->first();
        if ($defaultEmailSMTPSettings == null) {
            return  response()->json(['Message' => 'Not able to send email please review your SMTP Settings.'], 409);
        }

        $appuser->resetPasswordCode = Str::uuid(36);

        $appuser->save();

        try {
            $body = Storage::disk('public')->get('reset-password-template.html');
            
            // $body = file_get_contents($filePath);
            $link = $request->getSchemeAndHttpHost() . "/reset-password/" . $appuser->resetPasswordCode;
            $body = str_replace("##RESET_LINK##", $link, $body);
            $message = [
                'to_address' => $appuser->email,
                'subject' => 'Reset Password',
                'message' => $body,
                'path' => null,
            ];

            try {
                $this->emailRepository->sendEmail($message);
            } catch (\Exception $e) {
                return  response()->json(['Message' => 'Not able to send email please review your SMTP Settings.'], 409);
            }
        } catch (\Exception $e) {
            return response()->json([
                'Message' => 'Error while seding reset password link.' . $e->getMessage(),
            ], 409);
        }
    }

    public function getUserInfoForResetPassword($id)
    {
        $appuser = Users::where('resetPasswordCode', '=', $id)->first();
        if ($appuser == null) {
            return response()->json([
                'Message' => "Link is not correct or expired.",
            ], 409);
        }

        $user = [
            'id' => $appuser->id,
            'email' => $appuser->email
        ];

        return $user;
    }

    public function resetPassword($request)
    {
        $appuser = Users::where('email', '=', $request->userName)->first();
        if ($appuser == null) {
            return response()->json([
                'Message' => "Link is not correct or expired.",
            ], 409);
        }

        $appuser->password = Hash::make($request->password);
        $appuser->resetPasswordCode = null;
        $appuser->save();
        return [];
    }
}
