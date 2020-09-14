@extends('layouts.defaultMail')
@section('content')
    Olá <b>{{ $name }}</b>,
    <p>você fez um novo pedido em nosso sistema.</p>
    <p>Abaixo seguem os dados do pedido:</p>
    <h2>Descrição</h2>
    <table>
        <tr>
            <td><b>Número:</b></td>
            <td>{{ $order->id }}</td>
        </tr>
        <tr>
            <td><b>Data:</b></td>
            <td>{{ $order->created_at }}</td>
        </tr>
    </table>
    <h2>Itens</h2>
    @if ($order->orderProduct)
        <table>
            <tr>
                <th>Imagem</th>
                <th>Produto</th>
                <th>Descrição</th>
                <th>Quantidade</th>
                <th>Valor Unitário</th>
                <th>Valor Total</th>
            </tr>
            @foreach ($order->orderProduct as $product)
                <tr>
                    <td><img src="http://{{ env('APP_URL') }}/image/{{ $product->product->photo_name }} " alt="Girl in a jacket" width="100" height="100"></td>
                    <td>{{ $product->product->type->name }}</td>
                    <td>{{ $product->product->name }}</td>
                    <td>{{ $product->product_qtd }}</td>
                    <td>{{ $product->product->price }}</td>
                    <td>{{ $product->product_qtd * $product->product->price }}</td>
                </tr>
            @endforeach
        </table>
    @endif
@stop
