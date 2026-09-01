<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class NotificacionCobroAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $cobro;
    public $rutaPdf;

    public function __construct($cobro, $rutaPdf)
    {
        $this->cobro = $cobro;
        $this->rutaPdf = $rutaPdf;
    }

    public function build()
    {
        return $this->subject('Nuevo Cobro Registrado - Comprobante #' . $this->cobro->id)
                    ->html("
                        <h2>Nuevo cobro registrado</h2>
                        <p><strong>Cliente:</strong> {$this->cobro->nombre_cliente}</p>
                        <p><strong>Teléfono:</strong> {$this->cobro->telefono}</p>
                        <p><strong>Concepto:</strong> {$this->cobro->concepto}</p>
                        <p><strong>Monto:</strong> \${$this->cobro->monto}</p>
                        <p><strong>Mano de obra:</strong> {$this->cobro->mano_de_obra}</p>
                        <hr>
                        <p>Se adjunta el recibo generado en PDF.</p>
                    ")
                    ->attach($this->rutaPdf);
    }
}
