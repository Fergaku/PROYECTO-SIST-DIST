from flask import Flask, jsonify
import mysql.connector
import os

app = Flask(__name__)

def conectar():
    return mysql.connector.connect(
        host=os.getenv("DB_HOST"),
        database=os.getenv("DB_NAME"),
        user=os.getenv("DB_USER"),
        password=os.getenv("DB_PASSWORD")
    )

# --- VISTA WEB ---
@app.route('/reporte')
def dashboard_completo():
    conn = conectar()
    cur = conn.cursor(dictionary=True)

    # 1. Obtener bajo stock
    cur.execute("SELECT id, nombre, descripcion, cantidad, precio FROM productos WHERE cantidad < 5")
    bajo_stock_data = cur.fetchall()

    # 2. Obtener Top 5
    cur.execute("""
        SELECT id, nombre, cantidad, precio, (cantidad * precio) AS valor
        FROM productos
        ORDER BY valor DESC
        LIMIT 5
    """)
    top5_data = cur.fetchall()

    # 3. Obtener TODOS los productos (Para la tabla web)
    cur.execute("SELECT id, nombre, descripcion, cantidad, precio, (cantidad * precio) AS valor FROM productos ORDER BY id ASC")
    todos_productos_data = cur.fetchall()

    # 4. Obtener Resumen General
    cur.execute("SELECT COUNT(*), COALESCE(SUM(cantidad * precio), 0) FROM productos")
    resumen_raw = cur.fetchone()

    total_productos = resumen_raw['COUNT(*)'] if 'COUNT(*)' in resumen_raw else list(resumen_raw.values())[0]
    valor_total = resumen_raw['COALESCE(SUM(cantidad * precio), 0)'] if 'COALESCE(SUM(cantidad * precio), 0)' in resumen_raw else list(resumen_raw.values())[1]

    cur.close()
    conn.close()

    # Estilos y estructura de las tablas HTML
    html = """
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Reportes de Inventario Completo</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; background-color: #fafafa; color: #333; }
            h1 { color: #2c3e50; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; }
            h2 { color: #34495e; margin-top: 40px; }
            .resumen-box { background-color: #eedfcf; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 5px solid #d35400; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; background-color: white; margin-bottom: 20px; }
            th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
            th { background-color: #2c3e50; color: white; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            .alerta { background-color: #fce4e4; color: #cc0000; font-weight: bold; }
            .seccion-todos { background-color: #e8f4f8; }
            .seccion-todos th { background-color: #2980b9; }
        </style>
    </head>
    <body>
        <h1>Dashboard del Sistema de Reportes</h1>

        <div class="resumen-box">
            <p><strong>Total de Productos Registrados:</strong> """ + str(total_productos) + """</p>
            <p><strong>Valor Total del Inventario:</strong> $""" + f"{float(valor_total):,.2f}" + """</p>
        </div>

        <h2>⚠️ Alertas de Bajo Stock (&lt; 5 unidades)</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Descripción</th>
                <th>Cantidad en Stock</th>
                <th>Precio Unitario</th>
            </tr>
    """

    if bajo_stock_data:
        for p in bajo_stock_data:
            html += f"""
            <tr class="alerta">
                <td>{p['id']}</td>
                <td>{p['nombre']}</td>
                <td>{p['descripcion']}</td>
                <td>{p['cantidad']}</td>
                <td>${float(p['precio']):,.2f}</td>
            </tr>
            """
    else:
        html += "<tr><td colspan='5'>No hay productos con bajo stock actualmente.</td></tr>"

    html += """
        </table>

        <h2>📊 Top 5 Productos con Mayor Valor</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Valor Total (Cant x Precio)</th>
            </tr>
    """

    for p in top5_data:
        html += f"""
        <tr>
            <td>{p['id']}</td>
            <td>{p['nombre']}</td>
            <td>{p['cantidad']}</td>
            <td>${float(p['precio']):,.2f}</td>
            <td><strong>${float(p['valor']):,.2f}</strong></td>
        </tr>
        """

    html += """
        </table>

        <h2>📋 Inventario Completo (Todos los Productos)</h2>
        <table class="seccion-todos">
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Valor de Stock</th>
            </tr>
    """

    if todos_productos_data:
        for p in todos_productos_data:
            html += f"""
            <tr>
                <td>{p['id']}</td>
                <td><strong>{p['nombre']}</strong></td>
                <td>{p['descripcion']}</td>
                <td>{p['cantidad']}</td>
                <td>${float(p['precio']):,.2f}</td>
                <td>${float(p['valor']):,.2f}</td>
            </tr>
            """
    else:
        html += "<tr><td colspan='6'>No hay ningún producto registrado en el sistema.</td></tr>"

    html += """
        </table>
    </body>
    </html>
    """
    return html


# --- ENDPOINTS EN FORMATO JSON PURO ---

@app.route('/reporte/bajo-stock')
def bajo_stock():
    conn = conectar()
    cur = conn.cursor(dictionary=True)
    cur.execute("SELECT * FROM productos WHERE cantidad < 5")
    datos = cur.fetchall()
    cur.close()
    conn.close()
    return jsonify(datos)

@app.route('/reporte/top5')
def top5():
    conn = conectar()
    cur = conn.cursor(dictionary=True)
    cur.execute("SELECT nombre, cantidad, precio, (cantidad * precio) AS valor FROM productos ORDER BY valor DESC LIMIT 5")
    datos = cur.fetchall()
    cur.close()
    conn.close()
    return jsonify(datos)

@app.route('/reporte/resumen')
def resumen():
    conn = conectar()
    cur = conn.cursor()
    cur.execute("SELECT COUNT(*), COALESCE(SUM(cantidad * precio),0) FROM productos")
    datos = cur.fetchone()
    cur.close()
    conn.close()
    return jsonify({"total_productos": datos[0], "valor_total_inventario": float(datos[1])})

@app.route('/reporte/todos')
def todos_json():
    conn = conectar()
    cur = conn.cursor(dictionary=True)
    cur.execute("SELECT id, nombre, descripcion, cantidad, precio, (cantidad * precio) AS valor FROM productos ORDER BY id ASC")
    datos = cur.fetchall()
    cur.close()
    conn.close()
    return jsonify(datos)


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)