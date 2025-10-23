<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CatEmpresa;
use App\Models\CatGiro;

class CatEmpresasSeeder extends Seeder
{
    public function run()
    {
        // Obtener todos los giros para relacionarlos
        $giros = CatGiro::all();

        $empresas = [
            ['rfc'=>"AME750808D48",'nombre_empresa' => 'Adidas de México sa de cv', 'pagina_web' => 'https://retailedx.com/adidas/', 'giro' => 'Ropa'],
            ['rfc'=>"AER7108064A2",'nombre_empresa' => 'Aeromexico', 'pagina_web' => 'https://amfacturacion.aeromexico.com/', 'giro' => 'Línea Area'],
            ['rfc'=>"ALI010824PB5",'nombre_empresa' => 'Alisur sa de cv "KFC Villahermosa"', 'pagina_web' => 'https://sf.facelec.net:8443/autofactura/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Amapola', 'pagina_web' => 'https://facturacion.parrot.rest/billing/amapola-panaderia', 'giro' => 'Panadería'],
            ['rfc'=>"AAI580414GA9",'nombre_empresa' => 'American Airlines', 'pagina_web' => 'https://airlines.e-facturate.com/invoiceaa/', 'giro' => 'Línea Area'],
            ['rfc'=>"AGP740228EE9",'nombre_empresa' => 'Autobuses Golfo pacífico sa de cv', 'pagina_web' => 'http://taxis.grupoado.com.mx/emisionuniqo/', 'giro' => 'Taxi'],
            ['rfc'=>"AHF060131G59",'nombre_empresa' => 'Automotriz HF sa de cv', 'pagina_web' => 'https://permergas.com.mx/', 'giro' => 'Gasolinería'],
            ['rfc'=>"OFA9210138U1",'nombre_empresa' => 'Burger King', 'pagina_web' => 'https://alsea.interfactura.com/', 'giro' => 'Restaurante'],
            ['rfc'=>"CAE040616DY8",'nombre_empresa' => 'Caminante', 'pagina_web' => 'https://caminante.mx/introfact.html', 'giro' => 'Transporte'],
            ['rfc'=>"GRI2208249C0",'nombre_empresa' => 'Cancino Prado Norte', 'pagina_web' => 'https://facturacion.infocaja.com.mx/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Carola Tecnológico', 'pagina_web' => 'carolacafeteria@gmail.com', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Centro comercial santa fe', 'pagina_web' => 'https://centrosantafe.com.mx/pages/facturacion-electronica', 'giro' => 'Estacionamiento'],
            ['rfc'=>"",'nombre_empresa' => 'chilis', 'pagina_web' => 'https://alsea.interfactura.com/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Cocina abierta', 'pagina_web' => 'https://facturacion.grupomyt.mx/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Coco Canela', 'pagina_web' => 'https://www.wansoft.net/cococanela/fe.html', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Combustible Buenavista sa de cv', 'pagina_web' => 'https://br.gasolinamexico.mx/facturacion_tianguistenco/', 'giro' => 'Gasolinería'],
            ['rfc'=>"",'nombre_empresa' => 'Concesionario de vias troncales sa de cv', 'pagina_web' => 'https://www.facturacioncha-vta.com.mx/', 'giro' => 'Casetas'],
            ['rfc'=>"",'nombre_empresa' => 'Consorcio luna sa de cv', 'pagina_web' => 'http://www.cluna.com.mx/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Contramar', 'pagina_web' => 'https://redcontramar.detickets.mx/#/ticket/buscar', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Copa Airlines', 'pagina_web' => 'https://copamx.getyourinvoice.com/airline/CO', 'giro' => 'Línea Area'],
            ['rfc'=>"",'nombre_empresa' => 'Distribuidora Liverpool sa de cv', 'pagina_web' => 'https://facturacionclientes.liverpool.com.mx/inicio/&uid=liverpool', 'giro' => 'Tienda'],
            ['rfc'=>"",'nombre_empresa' => 'Dominos', 'pagina_web' => 'https://alsea.interfactura.com/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'El Califa Metepec', 'pagina_web' => 'http://facturacion.inforest.com.mx/default.aspx', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'El Cardenal', 'pagina_web' => 'https://www.iyaax.net/elcardenal/fe/V3/index.aspx', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'El Carnalito', 'pagina_web' => 'https://admin.softrestaurant.com/elcarnalitolaasuncion', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Estación de servicio Orizaba sa de cv', 'pagina_web' => 'https://facturasgas.com/facturacion/autofactura.php', 'giro' => 'Gasolinería'],
            ['rfc'=>"",'nombre_empresa' => 'Estacionamiento AICM T2', 'pagina_web' => 'https://www.aicm.com.mx/aicm/negocios/facturacion-electronica-cfdi', 'giro' => 'Estacionamiento'],
            ['rfc'=>"",'nombre_empresa' => 'Estacionamiento solnava', 'pagina_web' => 'https://syspark.com.mx/', 'giro' => 'Estacionamiento'],
            ['rfc'=>"",'nombre_empresa' => 'ETN turistar lujos sa de cv', 'pagina_web' => 'https://venta.etn.com.mx/facturacionelectronica/indexfacturaelec.html', 'giro' => 'Línea Transporte'],
            ['rfc'=>"",'nombre_empresa' => 'Fonda Yecapixtla Metepec', 'pagina_web' => 'facturacion.nortesur1@gmail.com', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Gas Imperial', 'pagina_web' => 'https://gasimperial.com/facturacion/', 'giro' => 'Gas'],
            ['rfc'=>"",'nombre_empresa' => 'Gasolinería Renovación', 'pagina_web' => 'https://bocgas.gasolinamexico.net/facturacion_bocgas/', 'giro' => 'Gasolinería'],
            ['rfc'=>"",'nombre_empresa' => 'Gasolinería Tecnológico', 'pagina_web' => 'https://www.erfc.com.mx/', 'giro' => 'Gasolinería'],
            ['rfc'=>"",'nombre_empresa' => 'H&M Hennes & Mauritz sa de cc', 'pagina_web' => 'https://webportal.edicomgroup.com/customers/hm/search.htm', 'giro' => 'Tienda'],
            ['rfc'=>"",'nombre_empresa' => 'Holiday inn VHSA AER', 'pagina_web' => 'https://facturacion-hivsa.nfact.mx/', 'giro' => 'Hotel'],
            ['rfc'=>"",'nombre_empresa' => 'Iberia', 'pagina_web' => 'https://www.iberia.com/mx/facturas/', 'giro' => 'Línea Area'],
            ['rfc'=>"",'nombre_empresa' => 'it´s just wings', 'pagina_web' => 'https://alsea.interfactura.com/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Italianis', 'pagina_web' => 'https://alsea.interfactura.com/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Jensa servicios alimenticios sa de cv', 'pagina_web' => 'http://taquearte.libellum.com.mx/Consideraciones.aspx', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Koku lerma', 'pagina_web' => 'https://www.wansoft.net/grupopatagonia/fe.html', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'La crepe Parisiene Galerias Metepec', 'pagina_web' => 'facturacion@lacrepeparisienne.com', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'La Española', 'pagina_web' => 'https://admin.softrestaurant.com/laespanola1', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'La Española', 'pagina_web' => 'https://mefacturo.mx/laespanola1', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Mexicana de aviación', 'pagina_web' => 'https://mexicana.gob.mx/facturaElectronica', 'giro' => 'Línea Area'],
            ['rfc'=>"",'nombre_empresa' => 'Moshi Moshi', 'pagina_web' => 'https://facturacion.grupomyt.mx/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'National Rent a Car', 'pagina_web' => 'https://nationalcar.com.mx/facturacion-en-linea', 'giro' => 'Transporte'],
            ['rfc'=>"",'nombre_empresa' => 'Nativa Shanti niddo café', 'pagina_web' => 'https://xetux-e.com/facturacion/webFact', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Nuevas cantinas mexicanas', 'pagina_web' => 'https://facturacion.grupomyt.mx/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'office Depot', 'pagina_web' => 'https://facturacion.officedepot.com.mx/#/home', 'giro' => 'Papelería'],
            ['rfc'=>"",'nombre_empresa' => 'Operadora de alimentos y malteadas sapi de cv', 'pagina_web' => 'https://efactura.shakeshack.com.mx/facturacion', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Operadora de trajes sa de cv', 'pagina_web' => 'https://cw.pos.mx/factura/vittorioforti', 'giro' => 'Tienda'],
            ['rfc'=>"",'nombre_empresa' => 'OPQR sa de cv', 'pagina_web' => 'https://alsea.interfactura.com/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Palacio de Hierro sa de cv', 'pagina_web' => 'https://palaciodehierro.cfdinova.com.mx/PortalFacturacionPH/', 'giro' => 'Tienda'],
            ['rfc'=>"",'nombre_empresa' => 'pf changs', 'pagina_web' => 'https://alsea.interfactura.com/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Porco Rosso BBQ S.A.P.I. de cv', 'pagina_web' => 'http://e-facpos.com/facturacion/PRB', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Promotora y administradora de carreteras', 'pagina_web' => 'https://www.pinfrafacturacion.com.mx/', 'giro' => 'Casetas'],
            ['rfc'=>"",'nombre_empresa' => 'Proveedora de alimentos liebe liebe sa de cv', 'pagina_web' => 'https://restlcdbc.com/genfactura/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Restaurante Fishers Toluca s de rl de cv', 'pagina_web' => 'http://cfdiv2.fishers.com.mx/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Restaurantes ADMX S DE RL DE CV', 'pagina_web' => 'https://www.facturacionmcdonalds.com.mx/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Servicio fácil del sureste sa de cv', 'pagina_web' => 'https://servifacil.ecsmexico.com:3001/facturacionweb/welcome', 'giro' => 'Gasolinería'],
            ['rfc'=>"",'nombre_empresa' => 'Servicio Mirusa sa de cv', 'pagina_web' => 'https://mirusa.gasolinamexico.net/facturacion_mirusa/', 'giro' => 'Gasolinería'],
            ['rfc'=>"",'nombre_empresa' => 'Servicio Mugomais', 'pagina_web' => 'https://g500network.com/facturacion-en-linea/', 'giro' => 'Gasolinería'],
            ['rfc'=>"",'nombre_empresa' => 'Servicio Portomarin sa de cv', 'pagina_web' => 'https://lugogas.com/', 'giro' => 'Gasolinería'],
            ['rfc'=>"",'nombre_empresa' => 'Servicio serrano sa de cv', 'pagina_web' => 'https://g500network.com/facturacion-en-linea/', 'giro' => 'Gasolinería'],
            ['rfc'=>"",'nombre_empresa' => 'Starbucks', 'pagina_web' => 'https://alsea.interfactura.com/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Subway', 'pagina_web' => 'https://clientes.facturassubway.mx/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Th de México sa de cv', 'pagina_web' => 'https://timhortonsmx.com/es/facturar/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'the chesscake factory', 'pagina_web' => 'https://alsea.interfactura.com/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Tiendas Cuadra', 'pagina_web' => 'https://cuadra.facturehoy.com/FhAutoFacturacionAdmin/facturacion/nuevaAutoFactura.htm', 'giro' => 'Zapatos'],
            ['rfc'=>"",'nombre_empresa' => 'Toks', 'pagina_web' => 'https://efactura.toks.com.mx/facturacion', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Vicente Metepec', 'pagina_web' => 'https://operadoraon.yomefacturo.mx/v2/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'Vips', 'pagina_web' => 'https://alsea.interfactura.com/', 'giro' => 'Restaurante'],
            ['rfc'=>"",'nombre_empresa' => 'viva aerobus', 'pagina_web' => 'https://facturacion.vivaaerobus.com/', 'giro' => 'Línea Area'],
            ['rfc'=>"",'nombre_empresa' => 'Volaris', 'pagina_web' => 'https://factura.volaris.com/', 'giro' => 'Línea Area'],
        ];

        foreach ($empresas as $empresaData) {
            // Buscar el giro correspondiente
            $giro = $giros->firstWhere('nombre', $empresaData['giro']);
            
            if ($giro) {
                CatEmpresa::create([
                    'rfc' => $empresaData['rfc'],
                    'nombre_empresa' => $empresaData['nombre_empresa'],
                    'pagina_web' => $empresaData['pagina_web'],
                    'id_giro' => $giro->id,
                    'activo' => true
                ]);
            }
        }
    }
}