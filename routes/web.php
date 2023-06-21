<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// [GUEST] VIEWS
Route::view('/', 'guest.login')->name('login');
Route::post('/login', 'AuthController@login')->name('auth.login');
Route::post('/logout', 'AuthController@logout')->name('auth.logout');
Route::post('/users', 'UserController@store')->name('users.store');
Route::post('/users-tutor', 'UserController@tutorStore')->name('users-tutor.store');

// [ALUNO] VIEWS
Route::prefix('/aluno')->middleware('auth')->group(function(){
  Route::view('/inicio', 'aluno.inicio')->name('aluno.inicio');
  Route::view('/turma/{id}', 'aluno.turma')->name('aluno.turma');
});

Route::get('/document/{id}', 'HomeController@document')->name('open-document');

// [TUTOR] VIEWS
Route::prefix('/tutor')->middleware('auth')->group(function(){
  Route::view('/turmas', 'tutor.turmas')->name('tutor.turmas');
  Route::view('/turma/{id}', 'tutor.turma')->name('tutor.turma');
  Route::view('/agenda', 'tutor.agenda')->name('tutor.agenda');
  Route::view('/barema/{id}', 'tutor.barema')->name('tutor.barema');
  Route::view('/problemas/adicionar/{id}','tutor.adicionar-problema' )->name('tutor.adicionar-problema');
  Route::view('/problemas/editar/{id}','tutor.editar-problema' )->name('tutor.editar-problema');
  Route::view('/problemas/pesquisar', 'tutor.pesquisar-problema')->name('tutor.pesquisar-problema');
  Route::view('/problema-nota/{problemaId}', 'tutor.problema-nota')->name('tutor.problema-nota');
});

// [ADMIN] VIEWS
Route::prefix('/admin')->middleware('auth')->group(function(){
  Route::view('/menu', 'adm.menu')->name('adm.menu');
  Route::view('/departamentos', 'adm.departamentos')->name('adm.departamentos');
  Route::view('/disciplinas', 'adm.disciplinas')->name('adm.disciplinas');
  Route::view('/semestres', 'adm.semestres')->name('adm.semestres');
  Route::view('/turmas', 'adm.turmas')->name('adm.turmas');
  Route::view('/tutores', 'adm.tutores')->name('adm.tutores');
  Route::view('/log', 'adm.log')->name('adm.log');
});

//ROTAS DE RETORNO DE DADOS
Route::middleware('auth')->group(function(){
    Route::apiResource('/baremas', 'BaremasController');

    Route::apiResource('/departamentos', 'DepartamentosController');

    Route::apiResource('/disciplinas', 'DisciplinasController');
    Route::get('/disciplinas/problema/{id}', 'DisciplinasController@problemas')->name("disciplinas.problemas");

    Route::apiResource('/semestres', 'SemestresController');
    Route::get('/semestres/disciplinas-ofertadas/{semestreId}', 'SemestresController@disciplinasOfertadas')->name("semestres.disciplinas");

    Route::get('/logs', 'SystemLogsController@index')->name('logs.index');

    Route::apiResource('/turmas', 'TurmasController');
    Route::get('/turma/tutor/{turmaId}', 'TurmasController@tutores')->name('turmas.tutor');

    Route::apiResource('/disciplinas-ofertadas', 'DisciplinaOfertadasController');
    Route::get('/disciplinas-ofertadas/problemas/{id}', 'DisciplinaOfertadasController@problemas')->name('disciplinas-ofertadas.problemas');

    Route::apiResource('/turma-tutor', 'TurmaTutorsController')->except('show');
    Route::get('/turma-tutor/problema-unidade/{turmaId}', 'TurmaTutorsController@problemaUnidade')->name('turma-tutor.problemas-unidade');
    Route::get('/turma-tutor/problemas-unidade', 'TurmaTutorsController@problemas')->name('turma-tutor.problemas');
    Route::get('/turma-tutor/turmas', 'TurmaTutorsController@turmas')->name('turma-tutor.turmas');

    Route::apiResource('/users', 'UserController')->except('store');
    Route::get('/users/type/{typeId}', 'UserController@getByType')->name('users.tipo');

    Route::apiResource('/problemas', 'ProblemasController')->except('update');
    Route::post('/problemas/editar/{id}', 'ProblemasController@update')->name('problemas.update');
    Route::post('/problemas/copiar', 'ProblemasController@copy')->name('problemas.copy');
    Route::post('/file', 'FileController@download')->name('file.download');

    Route::apiResource('/turma-alunos', 'TurmaAlunoController')->except(('store'));
    Route::get('/turma-alunos/turmas/{turmaId}', 'TurmaAlunoController@getStudentsByGrade')->name('turma-alunos.por-turma');
    Route::apiResource('/sessao', 'SessaoController');
    Route::post('/matricular-aluno/{sessionId}', 'TurmaTutorsController@matricular')->name('turma-alunos.matricular');

    Route::post('/presenca/{sessionId}', 'PresencaController@presenca')->name('presenca.store');

    Route::post('/nota/{problemaId}/{disciplinaOfertadaId}', 'ProblemaUnidadeController@applyNote')->name('problema-unidade.aplicar-nota');
    Route::get('/nota-aluno/{alunoId}/{disciplinaOfertadaId}', 'ProblemaUnidadeController@seeNote')->name('problema-unidade.ver-nota');
});