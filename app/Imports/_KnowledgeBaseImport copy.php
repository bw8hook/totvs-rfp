<?php

namespace App\Imports;

use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithStartRow;


class KnowledgeBaseImport_ implements ToModel, WithChunkReading, WithStartRow
{
    public $QtdRequisitos = 0;
    public function startRow(): int{
        return 2; // Ignora as duas primeiras linhas
    }


    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
            // // Valida os dados antes de salvar
            // Validator::make($row, [
            //     'name' => 'required|string|max:255',
            //     'email' => 'required|email|unique:users,email',
            // ])->validate();
        
            if($row[0] != "CLASSIFICAÇÃO 1"){
                $DadosBase = array();
                $DadosBase['classificacao1'] = $row['0'];
                $DadosBase['classificacao2'] = $row['1'];
                $DadosBase['requisito'] = $row['2'];
                $DadosBase['resposta'] = $row['3'];
                $DadosBase['resposta2'] = $row['4'];
                $DadosBase['importancia'] = $row['5'];
                $DadosBase['observacao'] = $row['6'];
                $DadosBase['produto'] = $row['7'];

                $this->QtdRequisitos = $this->QtdRequisitos++;

        


                if($row[0] != "Teste  5"){
                    dd($this->QtdRequisitos);
                }

                 // Salva os dados no banco
                // return new KnowledgeBase([
                //     'name'  => $row['name'],
                //     'email' => $row['email'],
                // ]);
            
            }
    }


     /**
     * Define o número de linhas por chunk.
     *
     * @return int
     */
    public function chunkSize(): int
    {
        return 100; // Processa 100 linhas por vez
    }

}
