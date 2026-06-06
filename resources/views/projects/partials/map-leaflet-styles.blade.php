<style>
    .crm-map-pin { width:28px;height:36px;position:relative;display:flex;align-items:flex-end;justify-content:center; }
    .crm-map-pin-body { width:22px;height:22px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:2px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.35); }
    .crm-map-pin.project .crm-map-pin-body { background: {{ $themeColor ?? '#4f46e5' }}; }
    .crm-map-pin.unit .crm-map-pin-body { background: #f59e0b; }
    .crm-map-pin.landmark .crm-map-pin-body { background: #6366f1; }
    .crm-map-pin.entrance .crm-map-pin-body { background: #10b981; }
    .crm-map-wrap { position: relative; }
    .crm-map-fullscreen-btn {
        position: absolute; top: 10px; left: 10px; z-index: 1000;
        background: #fff; border: 2px solid #e5e7eb; border-radius: 10px;
        padding: 6px 12px; font-size: 12px; font-weight: 700; cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,.12); font-family: Tajawal, sans-serif;
    }
    .crm-map-modal { position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,.55); display: none; align-items: center; justify-content: center; padding: 16px; }
    .crm-map-modal.open { display: flex; }
    .crm-map-modal-box { background: #fff; border-radius: 16px; width: 100%; max-width: 1200px; max-height: 92vh; overflow: hidden; display: flex; flex-direction: column; }
    .crm-map-modal-head { padding: 12px 16px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; font-family: Tajawal, sans-serif; }
    .crm-map-modal-body { flex: 1; min-height: 0; }
    .crm-map-modal-body .leaflet-container, .crm-map-modal-body iframe { width: 100%; height: 70vh; min-height: 400px; border: 0; }
</style>
