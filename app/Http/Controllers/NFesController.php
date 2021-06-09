<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\NFe;
use App\Models\Company;
use App\Models\Product;
use App\Models\NFeProduct;
use App\Models\Customer;
use App\Models\CustomerNFe;

class NFesController extends Controller {
  public function index() {
    $nfes = DB::table('nfe_product as mid')
      ->join('nfes as nfe', 'mid.nfe_id', '=', 'nfe.id')
      ->join('companies as com', 'nfe.company_id', '=', 'com.id')
      ->select(
        'nfe.id',
        'nfe.nfe_code',
        'nfe.generated_date',
        'nfe.delivery_price',
        'com.CNPJ',
        'com.name',
      )
      ->groupBy('mid.nfe_id')
      ->get();

    foreach($nfes as $nfe) {
      $nfe->total_price = DB::table('nfe_product as mid')
        ->join('products as pro', 'mid.product_id', '=', 'pro.id')
        ->groupBy('mid.nfe_id')
        ->having('mid.nfe_id', '=', $nfe->id)
        ->select(DB::raw('sum(pro.price * mid.quantity) as total'))
        ->get()[0]->total + $nfe->delivery_price;
    }

    return view('nfes.index', [
      'nfes' => $nfes,
    ]);
  }

  public function new() {
    return view('nfes.new');
  }

  public function show($slug) {
    $nfe = DB::table('nfes as nfe')
      ->join('companies as com', 'nfe.company_id', '=', 'com.id')
      ->select('*')
      ->where('nfe.id', '=', $slug)
      ->get()[0] ?? null;
    
    if (!$nfe) abort(404);

    $products = DB::table('nfe_product as mid')
      ->join('products as pro', 'mid.product_id', '=', 'pro.id')
      ->select(
        'pro.name',
        'pro.price',
        'mid.quantity',
        DB::raw('pro.price * mid.quantity as total'),
      )
      ->where('mid.nfe_id', '=', $slug)
      ->get();
        
    $total_price = $nfe->delivery_price;
    foreach($products as $product) {
      $total_price += $product->total;
    }

    $customer = DB::table('customer_nfe as mid')
      ->join('customers as cus', 'mid.customer_id', '=', 'cus.id')
      ->select('cus.name', 'cus.email', 'cus.CPF', 'cus.CNPJ')
      ->where('mid.nfe_id', $slug)
      ->get()[0];

    return view('nfes.show', [
      'products' => $products,
      'total_price' => $total_price,
      'nfe' => $nfe,
      'customer' => $customer,
    ]);
  }

  public function store(Request $request) {
    $request->validate([
      'files' => 'required',
      'files.*' => function ($attribute, $value, $fail) {
        if ($value->getClientMimeType() !== 'text/xml') {
          $fail($value->getClientOriginalName().' não é um arquivo xml válido');
        }
      },
    ]);
        
    if($request->hasfile('files')) {
      foreach($request->file('files') as $file) {
        $xml_string = file_get_contents($file->getRealPath());
        $xml = simplexml_load_string($xml_string);
        $json = json_encode($xml);
        $array = json_decode($json, true);
        $nfe = array_key_exists('NFe', $array) ? $array['NFe'] : $array;

        $nfe_code = $nfe['infNFe']['@attributes']['Id'];
        $generated_date = str_replace('T', ' ', $nfe['infNFe']['ide']['dhEmi']);
        $generated_date = preg_replace('/-[0-9]{2}:[0-9]{2}/', '', $generated_date);

        if (count(NFe::where('nfe_code', $nfe_code)->get()) > 0) {
          continue;
        }

        $company_id = $this->handleCompany($nfe);
        $delivery_price = $this->handleProducts($nfe);

        NFe::create([
          'nfe_code' => $nfe_code,
          'generated_date' => $generated_date,
          'delivery_price' => $delivery_price,
          'company_id' => $company_id,
        ]);

        $nfe_id = DB::table('nfes')
          ->where('nfe_code', '=', $nfe_code)
          ->select('id')
          ->get()[0]->id;

        $this->handleNfeProductRelation($nfe, $nfe_id);
        $this->handleCustomer($nfe, $nfe_id);
      }
    }

    return redirect('/dashboard/nfes');
  }

  public function handleCompany($array) {
    $company_cnpj = $array['infNFe']['emit']['CNPJ'];
    $name = $array['infNFe']['emit']['xFant']
      ?? $array['infNFe']['emit']['xNome'];

    $companies = DB::table('companies')
      ->where('CNPJ', $company_cnpj)
      ->select('id')
      ->get();

    if (count($companies) <= 0) {
      DB::table('companies')->insert([
        'CNPJ' => $company_cnpj,
        'name' => $name,
      ]);

      return DB::table('companies')
        ->where('CNPJ', $company_cnpj)
        ->select('id')
        ->get()[0]->id;
    }

    return $companies[0]->id;
  }

  public function saveProduct($product) {
    $name = $product['prod']['xProd'];
    $product_code = $product['prod']['cProd'];
    $price = $product['prod']['vUnTrib'];

    $delivery_price = $product['prod']['vFrete'] ?? 0;

    if (count(Product::where('product_code', $product_code)->get()) == 0) {
      DB::table('products')
        ->insert([
          'name' => $name,
          'product_code' => $product_code,
          'price' => $price,
        ]
      );
    }

    return $delivery_price;
  }

  public function handleProducts($array) {
    $products = $array['infNFe']['det'];

    // Único produto
    if (array_key_exists('@attributes', $products)) {
      return $this->saveProduct($products);
    }
    // Vários produtos
    else { 
      $total_price = 0;
      foreach($products as $product) {
        $total_price += $this->saveProduct($product);
      }
      return $total_price;
    }
  }

  public function saveNfeProduct($product, $nfe_id) {
    $product_code = $product['prod']['cProd'];
    $quantity = $product['prod']['qTrib'];

    $product_id = DB::table('products')
      ->where('product_code', '=', $product_code)
      ->select('id')
      ->get()[0]->id;
    
    DB::table('nfe_product')
      ->insert([
        'nfe_id' => $nfe_id,
        'product_id' => $product_id,
        'quantity' => $quantity,
      ]
    );
  }

  public function handleNfeProductRelation($array, $nfe_id) {
    $products = $array['infNFe']['det'];

    // Único produto
    if (array_key_exists('@attributes', $products)) {
      $this->saveNfeProduct($products, $nfe_id);
    }
    // Vários produtos
    else { 
      foreach($products as $product) {
        $this->saveNfeProduct($product, $nfe_id);
      }
    }
  }

  public function handleCustomer($array, $nfe_id) {
    $name = $array['infNFe']['dest']['xNome'];
    $cpf = $array['infNFe']['dest']['CPF'] ?? '';
    $cnpj = $array['infNFe']['dest']['CNPJ'] ?? '';
    $email = $array['infNFe']['dest']['email'] ?? '';

    $customer_id = DB::table('customers')
      ->select('id')
      ->where('CPF', $cpf)
      ->orWhere('CNPJ', $cnpj)
      ->get()[0]->id ?? null;

    if (!$customer_id) {
      DB::table('customers')
        ->insert([
          'name' => $name,
          'CPF' => $cpf,
          'CNPJ' => $cnpj,
          'email' => $email,
        ]);

      $customer_id = DB::table('customers')
        ->select('id')
        ->where('CPF', $cpf)
        ->orWhere('CNPJ', $cnpj)
        ->get()[0]->id;
    }

    CustomerNFe::create([
      'customer_id' => $customer_id,
      'nfe_id' => $nfe_id,
    ]);
  }
}
