import openpyxl

def fix_excel_complete(file_path):
    wb = openpyxl.load_workbook(file_path)

    # ================================================================
    # 1. INVERSION TOTAL - Llenar activos diferidos y capital intelectual
    # ================================================================
    ws_inv = wb['INVERSION TOTAL']
    
    # Columnas: B=2025, C=2026, D=2027, E=2028, F=2029
    # Activos fijos (solo año 1 = inversión inicial)
    ws_inv['B7'] = 4500000   # Equipos de computo
    ws_inv['B8'] = 3500000   # Redes
    ws_inv['B9'] = 85000000  # Vehiculos
    ws_inv['B10'] = 145000000 # Maquinaria (camara de frio, bascula, seleccionadora)
    ws_inv['B11'] = 8000000  # Muebles y Enseres
    ws_inv['B12'] = 6500000  # Software (FLOR-Net)
    
    # Activos Diferidos (Fila 15, 16) - Solo año 1
    ws_inv['A15'] = 'Contratos de Servicio'
    ws_inv['B15'] = 3200000
    ws_inv['A16'] = 'Constitucion Legal'
    ws_inv['B16'] = 1500000

    # Capital Intelectual (Fila 19) - Solo año 1
    ws_inv['A19'] = 'Normas ISO y Certificaciones INVIMA'
    ws_inv['B19'] = 12000000

    # Subtotales
    ws_inv['B13'] = ws_inv['B7'].value + ws_inv['B8'].value + ws_inv['B9'].value + ws_inv['B10'].value + ws_inv['B11'].value + ws_inv['B12'].value
    ws_inv['B17'] = ws_inv['B15'].value + ws_inv['B16'].value
    ws_inv['B20'] = ws_inv['B19'].value
    ws_inv['B21'] = ws_inv['B13'].value + ws_inv['B17'].value + ws_inv['B20'].value

    # Años 2026-2029: sin nueva inversión (mantenimiento maquinaria 5% anual)
    for j, col in enumerate(['C', 'D', 'E', 'F']):
        mant = int(ws_inv['B10'].value * 0.05 * (1.05 ** j))
        ws_inv[f'{col}10'] = mant
        ws_inv[f'{col}13'] = mant
        ws_inv[f'{col}17'] = 0
        ws_inv[f'{col}20'] = 0
        ws_inv[f'{col}21'] = mant

    # ================================================================
    # 2. FLUJO DE INGRESOS - Valores ANUALES reales
    # ================================================================
    ws_ing = wb['FLUJO DE INGRESOS']

    # Producto 1: Frutas Amazonicas
    # Año 1: 6000 kg/mes x 12 = 72000 kg anuales a $9500/kg = $684,000,000
    # Crecimiento: 8% unidades/año, 5% precio/año
    p1_unidades_y1 = 72000   # kg por año
    p1_precio_y1   = 9500    # COP por kg

    # Producto 2: Hortalizas
    # Año 1: 4000 kg/mes x 12 = 48000 kg anuales a $4500/kg = $216,000,000
    p2_unidades_y1 = 48000
    p2_precio_y1   = 4500

    for j, col in enumerate(['B', 'C', 'D', 'E', 'F']):
        u1 = int(p1_unidades_y1 * (1.08 ** j))
        pr1 = int(p1_precio_y1 * (1.05 ** j))
        ws_ing[f'{col}5'] = u1
        ws_ing[f'{col}6'] = pr1
        ws_ing[f'{col}7'] = u1 * pr1

        u2 = int(p2_unidades_y1 * (1.08 ** j))
        pr2 = int(p2_precio_y1 * (1.05 ** j))
        ws_ing[f'{col}9']  = u2
        ws_ing[f'{col}10'] = pr2
        ws_ing[f'{col}11'] = u2 * pr2

        ws_ing[f'{col}12'] = (u1 * pr1) + (u2 * pr2)

    # ================================================================
    # 3. FLUJO DE EGRESOS - Valores ANUALES (multiplicar por 12)
    # ================================================================
    sname = 'FLUJO DE EGRESOS ' if 'FLUJO DE EGRESOS ' in wb.sheetnames else 'FLUJO DE EGRESOS'
    ws_egr = wb[sname]

    # Costos de Producción (ya estaban fijados mensualmente, ahora anualizamos)
    # Materias primas ~35% ventas anuales, energia 5M/mes, seguros 2M/mes
    for j, col in enumerate(['B', 'C', 'D', 'E', 'F']):
        ventas_anuales = ws_ing[f'{col}12'].value
        ws_egr[f'{col}5'] = int(ventas_anuales * 0.35)  # Materias primas 35% ventas
        ws_egr[f'{col}6'] = int(5000000 * 12 * (1.06 ** j))  # Energia anual
        ws_egr[f'{col}7'] = int(2000000 * 12 * (1.06 ** j))  # Seguros anual

        # Nomina: SMMLV 2025 = $1,423,500. Con prestaciones ~x1.5 = $2,135,250/persona/mes
        # 9 personas x 12 meses
        smmlv = 1423500 * 1.5 * 9 * 12 * (1.06 ** j)
        ws_egr[f'{col}4'] = int(smmlv)

        # Costos Comercialización y Ventas ANUALES
        ws_egr[f'{col}11'] = int(15000000 * (1.05 ** j))  # Publicidad anual
        ws_egr[f'{col}12'] = int(36000000 * (1.05 ** j))  # Vendedores (3 x 1M/mes x 12)
        ws_egr[f'{col}13'] = int(12000000 * (1.05 ** j))  # Empaque anual
        ws_egr[f'{col}14'] = int(24000000 * (1.05 ** j))  # Distribucion anual

    # ================================================================
    # 4. ESTADO DE RESULTADOS - Corrección completa y sincronización
    # ================================================================
    ws_est = wb['ESTADO DE RESULTADOS']

    for j, col in enumerate(['B', 'C', 'D', 'E', 'F']):
        ventas = ws_ing[f'{col}12'].value

        # Costos de producción = materias primas + energia + seguros
        mat_prim = ws_egr[f'{col}5'].value
        energia  = ws_egr[f'{col}6'].value
        seguros  = ws_egr[f'{col}7'].value
        c_prod   = mat_prim + energia + seguros

        # Costos de comercialización y ventas (FINALMENTE LLENOS)
        c_comm = (ws_egr[f'{col}11'].value + ws_egr[f'{col}12'].value +
                  ws_egr[f'{col}13'].value + ws_egr[f'{col}14'].value)

        costos_directos = c_prod + c_comm
        margen_bruto    = ventas - costos_directos

        # Costos de administración = nómina anual
        g_admin = ws_egr[f'{col}4'].value

        u_oper = margen_bruto - g_admin
        tax    = int(u_oper * 0.35) if u_oper > 0 else 0
        u_neta = u_oper - tax

        ws_est[f'{col}4']  = ventas
        ws_est[f'{col}5']  = c_prod
        ws_est[f'{col}6']  = c_comm          # ← COSTOS COMERCIALIZACION Y VENTAS LLENOS
        ws_est[f'{col}7']  = costos_directos
        ws_est[f'{col}8']  = margen_bruto
        ws_est[f'{col}10'] = g_admin
        ws_est[f'{col}11'] = u_oper
        ws_est[f'{col}12'] = tax
        ws_est[f'{col}13'] = u_neta

    wb.save(file_path)

    # Verificación rápida
    print("=== VERIFICACION FINAL ===")
    print(f"VENTAS AÑO 1: ${ws_est['B4'].value:,.0f}")
    print(f"C.COMERCIALIZACION AÑO 1: ${ws_est['B6'].value:,.0f}")
    print(f"UTILIDAD NETA AÑO 1: ${ws_est['B13'].value:,.0f}")
    print(f"INVERSION TOTAL AÑO 1: ${wb['INVERSION TOTAL']['B21'].value:,.0f}")
    print("Excel corregido exitosamente.")

if __name__ == "__main__":
    fix_excel_complete('PLAN DE NEGOCIOS FORMULAS.xlsx')
