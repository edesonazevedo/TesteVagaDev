<?php

class Arquivo 
{
    
    public function veiricarDiretorio()
    {
        
        $diretorioin = "data/in/";
        // $diretorioout = storage_path('data/out');
        
        $diretorio = dir($diretorioin);
        
        while( $arquivo = $diretorio->read() ) {
            // ignora arquivos ocultos do sistema
            if ( ( $arquivo != ".") && ( $arquivo != ".." ) ) {
                if ( pathinfo( $arquivo,PATHINFO_EXTENSION ) == "dat" ) {
                    $dados = $this->lerArquivo($diretorioin.$arquivo);
                }
            }
        }
        
        $diretorio->close();

        return $dados;
    }
    
    /**
     * FAz a leitura do arquivo de dados
     *
     * @return array
     */
    public function lerArquivo( $arquivo )
    {
        // return $arquivo;
        // Abre o arquivo como leitura
        $arquivo = fopen( $arquivo , 'a+' );
        // Percorre todas as linhas do arquivo
        while ( !feof( $arquivo ) ) {

            // Pega o resultado da linha atual
            $ln = fgets( $arquivo, 4096 );

            // Ignora linhas em branco
            if ( $ln != '' ) {
                // Separa as informações";"
                $ln = explode('ç', $ln);

                switch ( $ln[0] ) {
                    case '001':
                        $dados['vendedores'][] = $ln;
                    break;
                    case '002':
                        $dados['clientes'][] = $ln;
                    break;
                    case '003':
                        $dados['vendas'][$ln[1]] = [$ln[0],$ln[1],$ln[3]];

                        $ln[2] = str_replace(']','', trim($ln[2]));
                        $ln[2] = str_replace('[','', $ln[2]);
                        $ln[2] = explode(',',$ln[2]);

                        $itens = array();
                        foreach ($ln[2] as $item) {
                            $item = explode('-',$item);
                            $produto['id'] = $item[0];
                            $produto['qtd'] = $item[1];
                            $produto['vlr'] = (float) $item[2];
                            $itens[] = $produto;
                        }

                        $dados['vendas'][$ln[1]]['itens'] = $itens;
                        
                        break;
                        
                    break;
                }    
            }    
        }
        
        // Fecha o arquivo aberto
        fclose($arquivo);

        $this->gravaArquivo($dados);

        // Retorna os dados do arquivo
        return $dados;
    }


     /**
     * Grava o arquivo de relatório no diretorio de saida
     */
    public function gravaArquivo( $dados ) {

        $qtdClientes = count($dados['clientes']);
        $qtdVendedores = count($dados['vendedores']);

        $idVendaMaisCara = '';
        $piorVendedor = '';
        $vlr = 0;


        foreach ( $dados['vendas'] as $venda ) {
            foreach ( $venda['itens'] as $item ) {
                if ( $item['vlr'] > $vlr ) {
                    $vlr = $item['vlr'];
                    $idVendaMaisCara = $venda[1];
                } else {
                    $piorVendedor = $venda[2];
                }
           }
        }


        $relatorio = "Quantidade de clientes    : ".$qtdClientes."\n";
        $relatorio .= "Quantidade de vendedores   : ".$qtdVendedores."\n";
        $relatorio .= "ID venda mais cara       : ".$idVendaMaisCara."\n";
        $relatorio .= "Pior vendedor de todos os tempos : ".$piorVendedor;

        file_put_contents('data/out/relatorio.done.dat',$relatorio);
    }
}


?>