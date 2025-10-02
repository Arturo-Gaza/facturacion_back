
    <div className="min-h-screen bg-background">
      {/* Header */}
      <header className="border-b border-border bg-card shadow-sm">
        <div className="container mx-auto px-6 py-4">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-destructive to-red-600 flex items-center justify-center">
              <Trash2 className="w-6 h-6 text-white" />
            </div>
            <div>
              <h1 className="text-2xl font-bold text-foreground">Correo de Eliminación de Cuenta</h1>
              <p className="text-sm text-muted-foreground">Recupera Gastos - Confirmación de Eliminación Permanente</p>
            </div>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="container mx-auto px-6 py-8">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Controls Panel */}
          <div className="lg:col-span-1 space-y-6">
            <Card className="border-destructive/20">
              <CardHeader>
                <CardTitle className="flex items-center gap-2 text-destructive">
                  <AlertTriangle className="w-5 h-5" />
                  Variables del Email
                </CardTitle>
                <CardDescription>Personaliza los datos del correo de eliminación</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="nombre">Nombre del Usuario</Label>
                  <Input
                    id="nombre"
                    value={nombre}
                    onChange={(e) => setNombre(e.target.value)}
                    placeholder="Nombre del usuario"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="codigo">Código de Confirmación</Label>
                  <Input
                    id="codigo"
                    value={codigo}
                    onChange={(e) => setCodigo(e.target.value)}
                    placeholder="Código"
                    className="font-mono"
                  />
                </div>

                <Button 
                  variant="destructive"
                  className="w-full" 
                  onClick={() => {
                    const newCode = Math.random().toString(36).substring(2, 8).toUpperCase();
                    setCodigo(newCode);
                  }}
                >
                  Generar Código Aleatorio
                </Button>
              </CardContent>
            </Card>

            <Card className="border-destructive/20 bg-destructive/5">
              <CardHeader>
                <CardTitle className="text-destructive flex items-center gap-2">
                  <Trash2 className="w-5 h-5" />
                  Información Importante
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-3 text-sm">
                <div className="flex items-start gap-2">
                  <AlertTriangle className="w-4 h-4 mt-0.5 text-destructive flex-shrink-0" />
                  <div className="space-y-2">
                    <p className="font-medium text-destructive">
                      Eliminación Permanente de Cuenta
                    </p>
                    <p className="text-muted-foreground">
                      Este correo confirma la eliminación permanente e irreversible de una cuenta de usuario.
                    </p>
                  </div>
                </div>
                
                <div className="pt-2 space-y-2 text-muted-foreground">
                  <p className="font-medium">⚠️ Acciones irreversibles:</p>
                  <ul className="list-disc list-inside space-y-1 text-xs">
                    <li>Todos los datos serán eliminados</li>
                    <li>No es posible recuperar la información</li>
                    <li>Los gastos y documentos se borrarán</li>
                    <li>El usuario no podrá reactivar su cuenta</li>
                  </ul>
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Preview Panel */}
          <div className="lg:col-span-2">
            <Card className="h-[calc(100vh-12rem)]">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Mail className="w-5 h-5" />
                  Vista Previa del Email
                </CardTitle>
                <CardDescription>
                  Así es como se verá el correo de eliminación en la bandeja del usuario
                </CardDescription>
              </CardHeader>
              <CardContent className="h-[calc(100%-5rem)] p-0">
                <EmailPreview type="delete" nombre={nombre} codigo={codigo} />
              </CardContent>
            </Card>
          </div>
        </div>
      </main>
    </div>

