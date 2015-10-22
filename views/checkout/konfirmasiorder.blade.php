<style type="text/css">.detail{margin-left: 8px}</style>
<div id="tagline" class="title tleft"><section><h2>Konfirmasi Order</h2></section></div>

<section>
    <article>
        <table class="table-minimal">
            <thead>
                <tr>
                    <th>Kode Order</th>
                    <th>Tanggal Order</th>
                    <th>Detail Order</th>
                    <th>Jumlah</th>
                    <th>Jumlah yg belum dibayar</th>
                    <th>No. Resi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$checkouttype==1 ? prefixOrder().$order->kodeOrder : prefixOrder().$order->kodePreorder}}</td>
                    <td>{{$checkouttype==1 ? waktu($order->tanggalOrder) : waktu($order->tanggalPreorder)}}</td>
                    <td>
                        <ul>
                        @if ($checkouttype==1)
                            @foreach ($order->detailorder as $detail)
                            <li class="detail">{{$detail->produk->nama}} {{$detail->opsiSkuId !=0 ? '('.$detail->opsisku['opsi1'].($detail->opsisku['opsi2'] != '' ? ' / '.$detail->opsisku['opsi2']:'').($detail->opsisku['opsi3'] !='' ? ' / '.$detail->opsisku['opsi3']:'').')':''}} - {{$detail->qty}}</li>
                            @endforeach
                        @else
                            <li class="detail">{{$order->preorderdata->produk->nama}} ({{$order->opsiSkuId==0 ? 'No Opsi' : $order->opsisku['opsi1'].($order->opsisku['opsi2']!='' ? ' / '.$order->opsisku['opsi2']:'').($order->opsisku['opsi3']!='' ? ' / '.$order->opsisku['opsi3']:'')}}) - {{$order->jumlah}}</li>
                        @endif
                        </ul>
                    </td>
                    <td>
                        @if($checkouttype==1)
                            {{price($order->total)}}
                        @else 
                            @if($order->status < 2)
                                {{price($order->total)}}
                            @elseif(($order->status > 1 && $order->status < 4) || $order->status==7)
                                {{price($order->total - $order->dp)}}
                            @else
                            0
                            @endif
                        @endif
                    </td>
                    <td>{{($order->status==2 || $order->status==3) ? price(0) : price($order->total)}}</td>
                    <td>{{ $order->noResi}}</td>
                    <td class="total-price">
                        @if($checkouttype==1)
                            @if($order->status==0)
                            <span class="label label-warning">Pending</span>
                            @elseif($order->status==1)
                            <span class="label label-danger">Konfirmasi diterima</span>
                            @elseif($order->status==2)
                            <span class="label label-success">Pembayaran diterima</span>
                            @elseif($order->status==3)
                            <span class="label label-info">Terkirim</span>
                            @elseif($order->status==4)
                            <span class="label label-default">Batal</span>
                            @endif
                        @else 
                            @if($order->status==0)
                            <span class="label label-warning">Pending</span>
                            @elseif($order->status==1)
                            <span class="label label-danger">Konfirmasi DP diterima</span>
                            @elseif($order->status==2)
                            <span class="label label-info">DP terbayar</span>
                            @elseif($order->status==3)
                            <span class="label label-info">Menunggu pelunasan</span>
                            @elseif($order->status==4)
                            <span class="label label-success">Pembayaran lunas</span>
                            @elseif($order->status==5)
                            <span class="label label-info">Terkirim</span>
                            @elseif($order->status==6)
                            <span class="label label-default">Batal</span>
                            @elseif($order->status==7)
                            <span class="label label-info">Konfirmasi Pelunasan diterima</span>
                            @endif
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <div class="row">
            <div class="col-md-5">
            @if($order->jenisPembayaran==1)
                @if($checkouttype==1)                         
                {{Form::open(array('url'=> 'konfirmasiorder/'.$order->id, 'method'=>'put'))}}                            
                
                @else                         
                {{Form::open(array('url'=> 'konfirmasipreorder/'.$order->id, 'method'=>'put'))}}                           
                @endif
                <div class="form-group">
                  <label  class="control-label"> Nama Pengirim:</label>
                  <input type="text" class="form-control" placeholder="Nama Pengirim" name='nama' required>
                </div>
                <div class="form-group">
                  <label  class="control-label"> No Rekening:</label>
                  <input type="text" class="form-control" placeholder="No Rekening" name='noRekPengirim' required>
                </div>
                <div class="form-group">
                  <label  class="control-label"> Rekening Tujuan:</label>
                  <select name='bank' class="form-control">
                      <option value=''>-- Pilih Bank Tujuan --</option>
                      @foreach ($banktrans as $bank)
                      <option value="{{$bank->id}}">{{$bank->bankdefault->nama}} - {{$bank->noRekening}} - A/n {{$bank->atasNama}}</option>
                      @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label  class="control-label"> Jumlah:</label>
                  @if($checkouttype==1)        
                    <input type="text" class="form-control" placeholder="jumlah yg terbayar" name='jumlah' value='{{$order->status==0 ? $order->total : ""}}' required>
                  @else
                    @if($order->status < 2)
                      <input class="form-control" placeholder="Jumlah pembayaran" type="text" name='jumlah' value='{{$order->dp}}' required>
                    @elseif(($order->status > 1 && $order->status < 4) || $order->status==7)
                      <input class="form-control" placeholder="Jumlah pembayaran" type="text" name='jumlah' value='{{$order->total - $order->dp}}' required>
                    @endif
                  @endif
                </div>
                <button type="submit" class="btn btn-warning">Konfirmasi Order</button>
                {{Form::close()}}
            @endif
            </div>
        </div>
        <br>
        @if($paymentinfo!=null)
        <h3><center>Paypal Payment Details</center></h3><br>
        <hr>
        <div class="table-responsive">
          <table class='table table-bordered'>
              <tr>
                  <td>Payment Status</td><td>:</td><td>{{$paymentinfo['payment_status']}}</td>
              </tr>
              <tr>
                  <td>Payment Date</td><td>:</td><td>{{$paymentinfo['payment_date']}}</td>
              </tr>
              <tr>
                  <td>Address Name</td><td>:</td><td>{{$paymentinfo['address_name']}}</td>
              </tr>
              <tr>
                  <td>Payer Email</td><td>:</td><td>{{$paymentinfo['payer_email']}}</td>
              </tr>
              <tr>
                  <td>Item Name</td><td>:</td><td>{{$paymentinfo['item_name1']}}</td>
              </tr>
              <tr>
                  <td>Receiver Email</td><td>:</td><td>{{$paymentinfo['receiver_email']}}</td>
              </tr>
              <tr>
                  <td>Total Payment</td><td>:</td><td>{{$paymentinfo['payment_gross']}} {{$paymentinfo['mc_currency']}}</td>
              </tr>
          </table>
        </div>
        <p>Thanks you for your order.</p>
        <br>
        @endif 
      
        @if($order->jenisPembayaran==2)
          <h3><center>Konfirmasi Pemabayaran Via Paypal</center></h3><br>
          <p>Silakan melakukan pembayaran dengan paypal Anda secara online via paypal payment gateway. Transaksi ini berlaku jika pembayaran dilakukan sebelum {{$expired}}. Klik tombol "Bayar Dengan Paypal" di bawah untuk melanjutkan proses pembayaran.</p>
          {{$paypalbutton}}
          <br>
        @endif
    </article>
</section>