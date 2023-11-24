<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // Validaciones
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'rut' => 'required|string|max:20|unique:users', // Asegura que el RUT sea único en la tabla de usuarios
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:8',
        ]);

        // Manejar errores de validación
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Crear y guardar el usuario si las validaciones pasan
        $user = new User();
        $user->name = $request->input('name');
        $user->surname = $request->input('surname');
        $user->rut = $request->input('rut');
        $user->email = $request->input('email'); 
        $user->password = Hash::make($request->input('password')); 
        $user->save();

        // Redirigir a la vista de éxito o a donde desees
        return response()->json(['message' => 'Usuario creado con éxito'], JsonResponse::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        // Validaciones
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'rut' => 'required|string|max:20|unique:users,rut,'.$id, // Asegura que el RUT sea único en la tabla de usuarios, excluyendo el usuario actual
            'email' => 'required|email|unique:users,email,'.$id, // Asegura que el email sea único en la tabla de usuarios, excluyendo el usuario actual
            'password' => 'string|min:8', // La contraseña es opcional en la actualización
        ]);

        // Manejar errores de validación para una API
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Buscar el usuario por ID
        $user = User::find($id);

        // Verificar si el usuario existe
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Actualizar los campos del usuario
        $user->name = $request->input('name');
        $user->surname = $request->input('surname');
        $user->rut = $request->input('rut');
        $user->email = $request->input('email');  

        // Actualizar la contraseña si se proporciona una nueva
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Guardar los cambios
        $user->save();

        // Puedes devolver una respuesta de éxito si es necesario
        return response()->json(['message' => 'Usuario actualizado con éxito'], JsonResponse::HTTP_OK);
    }

    public function destroy($id)
    {
        // Buscar el usuario por ID
        $user = User::find($id);

        // Verificar si el usuario existe
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Eliminar el usuario
        $user->delete();

        // Puedes devolver una respuesta de éxito si es necesario
        return response()->json(['message' => 'Usuario eliminado con éxito'], JsonResponse::HTTP_OK);
    }

    public function update_points($user_id, $points, $operation_type)
    {
        // TENER 2 TIPOS DE OPERACIONES SUMAR O RESTAR (SUMAR TIPO 0, RESTAR TIPO 1)
        $user = User::find($user_id);

        // Verificar si el usuario existe
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        switch ($operation_type) {
            case 0:
                $user->puntos += $points;
                break;
            case 1:
                $user->puntos -= $points;
                break;
            default:
                return response()->json(['error' => 'Tipo de operación no válido'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Guardar los cambios en la base de datos
        $user->save();

        return response()->json(['message' => 'Puntos actualizados con éxito', 'new_points' => $user->puntos], JsonResponse::HTTP_OK);
    }
    public function loginApi(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('name', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'status' => $user->status, // Agregar el atributo "status" a la respuesta
            ], 200);
        }

        // Agregar una verificación adicional para el usuario no encontrado
        $user = User::where('name', $request->name)->first();
        if (!$user) {
            return response()->json(['error' => 'usuario_no_encontrado'], 404);
        }

        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }
}
