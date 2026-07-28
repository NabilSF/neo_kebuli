import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import '../models/kbli_model.dart';
import '../services/application_service.dart';
import '../services/api_client.dart';
import 'tracking_screen.dart';

class ApplicationFormScreen extends StatefulWidget {
  final KbliItem? initialKbliItem;

  const ApplicationFormScreen({super.key, this.initialKbliItem});

  @override
  State<ApplicationFormScreen> createState() => _ApplicationFormScreenState();
}

class _ApplicationFormScreenState extends State<ApplicationFormScreen> {
  final _service = ApplicationService();
  final _bpsService = BpsApiService();
  final _formKey = GlobalKey<FormState>();

  final _productNameController = TextEditingController();
  final _materialController = TextEditingController();
  final _processController = TextEditingController();
  final _descController = TextEditingController();
  final _addressController = TextEditingController();

  KbliItem? _selectedKbli;
  bool _isSubmitting = false;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _selectedKbli = widget.initialKbliItem;
  }

  @override
  void dispose() {
    _productNameController.dispose();
    _materialController.dispose();
    _processController.dispose();
    _descController.dispose();
    _addressController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedKbli == null) {
      setState(() => _errorMessage = 'Pilih KBLI terlebih dahulu.');
      return;
    }

    setState(() {
      _isSubmitting = true;
      _errorMessage = null;
    });

    try {
      final app = await _service.submitApplication(
        kbliItemId: int.tryParse(_selectedKbli!.code) ?? 1,
        productName: _productNameController.text.trim(),
        productMaterial: _materialController.text.trim(),
        productionProcess: _processController.text.trim(),
        productDescription: _descController.text.trim(),
        sakerAddress: _addressController.text.trim(),
      );

      if (mounted) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (_) => TrackingScreen(initialCode: app.trackingCode),
          ),
        );
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Pengajuan berhasil! Kode Tracking: ${app.trackingCode}'),
            backgroundColor: AppColors.primary,
          ),
        );
      }
    } on ApiException catch (e) {
      if (mounted) {
        setState(() {
          _errorMessage = e.message;
          _isSubmitting = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _errorMessage = 'Gagal mengirim pengajuan. Periksa input Anda.';
          _isSubmitting = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Pengajuan Permohonan KBLI'),
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Formulir Pengajuan Baru',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.w800, color: AppColors.slate900),
              ),
              const SizedBox(height: 4),
              Text(
                'Lengkapi data produk untuk penelaahan klasifikasi baku usaha.',
                style: TextStyle(fontSize: 12, color: AppColors.slate400),
              ),
              const SizedBox(height: 24),

              if (_errorMessage != null) ...[
                Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: Colors.red.shade50,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: Colors.red.shade200),
                  ),
                  child: Text(_errorMessage!, style: TextStyle(color: Colors.red.shade700, fontSize: 13)),
                ),
                const SizedBox(height: 20),
              ],

              Text('Nama Produk', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppColors.slate900)),
              const SizedBox(height: 6),
              TextFormField(
                controller: _productNameController,
                decoration: InputDecoration(
                  hintText: 'Contoh: Minyak Goreng Kelapa Sawit',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                ),
                validator: (val) => val == null || val.trim().isEmpty ? 'Nama produk wajib diisi' : null,
              ),

              const SizedBox(height: 16),

              Text('Bahan Baku Utama', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppColors.slate900)),
              const SizedBox(height: 6),
              TextFormField(
                controller: _materialController,
                decoration: InputDecoration(
                  hintText: 'Contoh: Buah kelapa sawit segar',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                ),
                validator: (val) => val == null || val.trim().isEmpty ? 'Bahan baku wajib diisi' : null,
              ),

              const SizedBox(height: 16),

              Text('Proses Produksi', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppColors.slate900)),
              const SizedBox(height: 6),
              TextFormField(
                controller: _processController,
                maxLines: 2,
                decoration: InputDecoration(
                  hintText: 'Uraikan proses ekstraksi / pemrosesan',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                ),
                validator: (val) => val == null || val.trim().isEmpty ? 'Proses produksi wajib diisi' : null,
              ),

              const SizedBox(height: 16),

              Text('Deskripsi Produk & Uraian Tambahan', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppColors.slate900)),
              const SizedBox(height: 6),
              TextFormField(
                controller: _descController,
                maxLines: 3,
                decoration: InputDecoration(
                  hintText: 'Uraian spesifikasi teknis produk',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                ),
              ),

              const SizedBox(height: 16),

              Text('Alamat Satuan Kerja / Pabrik', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppColors.slate900)),
              const SizedBox(height: 6),
              TextFormField(
                controller: _addressController,
                decoration: InputDecoration(
                  hintText: 'Lokasi fasilitas produksi',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                ),
              ),

              const SizedBox(height: 32),

              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  onPressed: _isSubmitting ? null : _submit,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    elevation: 0,
                  ),
                  child: _isSubmitting
                      ? const SizedBox(
                          width: 24,
                          height: 24,
                          child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5),
                        )
                      : const Text('Kirim Pengajuan', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
