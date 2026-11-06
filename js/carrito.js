class Carrito {
  constructor() {
    this.items = [];
  }

  agregar(item) {
    const existente = this.items.find(x => x.producto_id === item.producto_id);
    if (existente) {
      existente.cantidad = item.stock ? Math.min(item.stock, existente.cantidad + item.cantidad) : existente.cantidad + item.cantidad;
    } else {
      this.items.push(item);
    }
  }

  quitar(id) {
    this.items = this.items.filter(x => x.producto_id !== id);
  }

  cambiar(id, cantidad) {
    const item = this.items.find(x => x.producto_id === id);
    if (!item) return;
    const max = item.stock || Infinity;
    item.cantidad = Math.max(1, Math.min(max, parseInt(cantidad || '1', 10) || 1));
  }

  total() {
    return this.items.reduce((suma, x) => suma + x.precio * x.cantidad, 0);
  }

  unidades() {
    return this.items.reduce((suma, x) => suma + x.cantidad, 0);
  }

  vacio() {
    return this.items.length === 0;
  }

  detalle() {
    return this.items.map(x => ({ producto_id: x.producto_id, cantidad: x.cantidad }));
  }
}
