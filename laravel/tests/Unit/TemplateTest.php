<?php

namespace Tests\Unit;

use App\Models\Template;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    public function test_model_can_be_created()
    {
        $template = new Template([
            'alias' => 'Simple Black on White',
            'filename' => '1678481493_simple_black-on-white.png',
            'file_path_original' => 'uploads/templates',
            'file_path_drawed_grid' => 'uploads/templates/drawed_grid',
            'file_path_drawed_grid_text' => 'uploads/templates/drawed_grid_text',
            'file_path_drawed_text' => 'uploads/templates/drawed_text',
            'width' => 960,
            'height' => 300,
        ]);

        $this->assertEquals('Simple Black on White', $template->alias);
        $this->assertEquals('1678481493_simple_black-on-white.png', $template->filename);
        $this->assertEquals('uploads/templates', $template->file_path_original);
        $this->assertEquals('uploads/templates/drawed_grid', $template->file_path_drawed_grid);
        $this->assertEquals('uploads/templates/drawed_grid_text', $template->file_path_drawed_grid_text);
        $this->assertEquals('uploads/templates/drawed_text', $template->file_path_drawed_text);
        $this->assertEquals(960, $template->width);
        $this->assertEquals(300, $template->height);
    }
}
