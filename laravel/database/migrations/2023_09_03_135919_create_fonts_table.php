<?php

use App\Models\Font;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Properties / Settings
     */
    protected string $upload_directory = 'uploads/fonts';

    /**
     * Callback function to only return TTF files.
     */
    public function is_ttf_file($filename)
    {
        return preg_match("/\.ttf$/", $filename);
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fonts', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->timestamps();
        });

        if (! file_exists(public_path($this->upload_directory))) {
            mkdir(public_path($this->upload_directory), 0755, true);
        }

        foreach (array_filter(Storage::disk('public')->files('fonts/'), $this->is_ttf_file(...), ARRAY_FILTER_USE_BOTH) as $fontfile_path) {
            // Move the manually uploaded file from the old into the new directory
            rename(
                public_path($fontfile_path),
                public_path($this->upload_directory).DIRECTORY_SEPARATOR.basename($fontfile_path)
            );

            // Add a database entry for the TTF file
            Font::create(['filename' => basename($fontfile_path)]);
        }

        if (file_exists(public_path('fonts/.gitignore'))) {
            unlink(public_path('fonts/.gitignore'));
        }

        if (file_exists(public_path('fonts/'))) {
            rmdir(public_path('fonts/'));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        mkdir(public_path('fonts/'));
        file_put_contents(public_path('fonts/.gitignore'), '*.ttf');

        foreach (Font::all() as $font) {
            // Move the uploaded file from the new into the old directory
            rename(
                public_path($this->upload_directory).DIRECTORY_SEPARATOR.$font->filename,
                public_path('fonts/'.$font->filename)
            );
        }

        Schema::dropIfExists('fonts');
    }
};
