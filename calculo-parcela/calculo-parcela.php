<?php
/** 
* Plugin Name: Calculo de parcela
* Description: criar parcelas em campos personalizados
* Version: 1.0
* Author: Apalosqui Lucas
**/


function altera_parcela(){
    //Pega os produtos selecionados e salva em uma lista os seus ids respectivos
    $products = wc_get_products( get_the_ID() );
    foreach($products as $product){
        //verifica se o produto tem quantidade de parcelas definida
        if( ! get_post_meta( $product->get_id(), '03_valor_parcelas', true ) ) {
            //pega o valor do produto
            $valorTotal = $product->get_price();
            if(mb_strpos($valorTotal, ",") == true){
                $valorTotal = str_replace(",",".",$valorTotal);
            }
            //transforma o valor em double e divide por 12 parcelas
            $valorParcelado = round((double)$valorTotal / 12, 2);
            $valorParcelado = mascaraParcela($valorParcelado);
        }else{
            //caso o valor da parcela esteja especificado no produto
            $valorTotal = $product->get_price();
            if(mb_strpos($valorTotal, ",") == true){
                $valorTotal = str_replace(",",".",$valorTotal);
            }
            //pega a quantidade de parcelas definidas
            $parcelas = get_post_meta( $product->get_id(), '03_valor_parcelas', true );
            //retira o x e transforma a parcela em inteiro
            $parcelas = str_replace("x","",$parcelas);
            //transforma o valor em double e divide pelas parcelas selecionadas
            $valorParcelado = round((double)$valorTotal / (int)$parcelas, 2);
            $valorParcelado = mascaraParcela($valorParcelado);
        }
        //atualiza o valor parcelado no campo customizado do produto
        update_post_meta($product->get_id(), '04_valor_parcelado', $valorParcelado);
    }
}

//transforma o valor a ser exibido da parcela
function mascaraParcela($valorParcelado){
    //verifica se contem . no valor da parcela
    if (mb_strpos($valorParcelado, '.') == true) {
        //separa a parcela e verifica se a parcela precisa adicionar zeros ou nao
        $teste = explode(".",$valorParcelado);
        if(strlen($teste[1]) == 1){
            //adiciona um 0 ao final do valor e troca os pontos por virgulas
           $valorParcelado = $valorParcelado . "0";
           return str_replace(".",",",$valorParcelado);
        }else{
            //altera os pontos pelas virgulas
            return str_replace(".",",",$valorParcelado);
        }
    }else{
        //adiciona virgula e zeros ao final da parcela
        return $valorParcelado . ",00";
    }
}

//ativa o script após um post ser atualizado
add_action('save_post', 'altera_parcela');
add_action('updated_postmeta', 'altera_parcela');