<?php

namespace Modules\Transporte\Http\Controllers\Api\Admin;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\Core\Services\ImageUploadService;
use Modules\Transporte\Criteria\VeiculoCriteria;
use Modules\Transporte\Http\Requests\VeiculoRequest;
use Modules\Transporte\Repositories\VeiculoRepository;
use Portal\Http\Controllers\BaseController;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * @resource API Regras de Acesso - Backend
 *
 * Essa API é responsável pelo gerenciamento de regras de Usuários no portal qImob.
 * Os próximos tópicos apresenta os endpoints de Consulta, Cadastro, Edição e Deleção.
 */
class VeiculoController extends BaseController
{

    /**
     * @var VeiculoRepository
     */
    private $veiculoRepository;
    /**
     * @var ImageUploadService
     */
    private $imageUploadService;

    public function __construct(
        VeiculoRepository $veiculoRepository,
        ImageUploadService $imageUploadService)
    {
        parent::__construct($veiculoRepository, VeiculoCriteria::class);
        $this->setPathFile(public_path('arquivos/img/veiculo'));
        $this->veiculoRepository = $veiculoRepository;
        $this->imageUploadService = $imageUploadService;
    }


    public function getValidator($id = null)
    {
        $this->validator = (new VeiculoRequest())->rules($id);
        return $this->validator;
    }

    public function veiculocadastro(Request $request){
        $data = $request->only([
            'transporte_marca_carro_id',
            'transporte_modelo_carro_id',
            'placa',
            'ano',
            'cor',
            'arquivo',
        ]);

        \Validator::make($data, [
            'transporte_marca_carro_id' =>'required|integer|exists:transporte_marca_carros,id',
            'transporte_modelo_carro_id' =>'required|integer|exists:transporte_modelo_carros,id',
            'ano' =>'required|integer',
            'placa' =>'required|string',
            'cor' =>'required|string',
            'arquivo' =>'required|string',
        ])->validate();

        try{
            $this->imageUploadService->upload64('arquivo', $this->getPathFile(), $data);
            $data['status'] = 'pendente';
            $data['user_id'] = $this->getUserId();
            return $this->veiculoRepository->create($data);
        }catch (ModelNotFoundException $e){
            return self::responseError(self::HTTP_CODE_NOT_FOUND, trans('errors.registre_not_found', ['status_code'=>$e->getCode(),'message'=>$e->getMessage()]));
        }
        catch (RepositoryException $e){
            return self::responseError(self::HTTP_CODE_NOT_FOUND, trans('errors.registre_not_found', ['status_code'=>$e->getCode(),'message'=>$e->getMessage()]));
        }
        catch (\Exception $e){
            return self::responseError(self::HTTP_CODE_BAD_REQUEST, trans('errors.undefined', ['status_code'=>$e->getCode(),'message'=>$e->getMessage()]));
        }
    }

    function updateImageVeiculo(Request $request, $id){
        $data = $request->only([
            'arquivo',
        ]);

        \Validator::make($data, ['arquivo'=>'required'])->validate();

        try{
            $veiculo = $this->veiculoRepository->findWhere([
                'user_id'=>$this->getUserId(),
                'id'=>$id
            ]);
            if(count($veiculo['data']) == 0){
                throw new \Exception('esse veiculo não pertence a voce!');
            }
            $first = current($veiculo['data']);
            $this->imageUploadService->upload64('arquivo', $this->getPathFile(), $data);
            $first['status'] = 'pendente';
            $first['num_passageiro'] = (int) $first['num_passageiro'];
            $this->veiculoRepository->update($first, $id);
            return parent::responseSuccess(parent::HTTP_CODE_OK, 'Obrigado!, Foto enviada em breve analisaremos!');
        }catch (ModelNotFoundException $e){
            return self::responseError(self::HTTP_CODE_NOT_FOUND, trans('errors.registre_not_found', ['status_code'=>$e->getCode(),'message'=>$e->getMessage()]));
        }
        catch (RepositoryException $e){
            return self::responseError(self::HTTP_CODE_NOT_FOUND, trans('errors.registre_not_found', ['status_code'=>$e->getCode(),'message'=>$e->getMessage()]));
        }
        catch (\Exception $e){
            return self::responseError(self::HTTP_CODE_BAD_REQUEST, trans('errors.undefined', ['status_code'=>$e->getCode(),'message'=>$e->getMessage()]));
        }

    }

    function todosByUser(){
        try{
            return $this->veiculoRepository->findWhere([
                'user_id'=>$this->getUserId()
            ]);
        }catch (ModelNotFoundException $e){
            return self::responseError(self::HTTP_CODE_NOT_FOUND, trans('errors.registre_not_found', ['status_code'=>$e->getCode(),'message'=>$e->getMessage()]));
        }
        catch (RepositoryException $e){
            return self::responseError(self::HTTP_CODE_NOT_FOUND, trans('errors.registre_not_found', ['status_code'=>$e->getCode(),'message'=>$e->getMessage()]));
        }
        catch (\Exception $e){
            return self::responseError(self::HTTP_CODE_BAD_REQUEST, trans('errors.undefined', ['status_code'=>$e->getCode(),'message'=>$e->getMessage()]));
        }
    }

    function cores(){
        $path = module_path('Transporte');
        return ['data'=>csv_load($path.'\Files\Cores\veiculo-cor.csv')];
    }

}
