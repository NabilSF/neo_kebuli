<?php

$replacements = [
    'ActivityLog.php' => [
        "        'module',\n" => "",
        "        'description',\n" => "",
        "        'old_values',\n" => "",
        "        'new_values',\n" => "",
        "        'user_agent',\n" => "",
        "        'module', " => "",
        "        'description', " => "",
        "        'old_values', " => "",
        "        'new_values', " => "",
        "        'user_agent', " => "",
    ],
    'ApplicationHistory.php' => [
        "'from_status'" => "'status'",
        "'to_status',\n" => "",
        "'to_status', " => "",
        "'changed_by'" => "'created_by'",
    ],
    'KbliCategory.php' => [
        "        'created_by',\n" => "",
        "        'created_by', " => "",
    ],
    'KbliItem.php' => [
        "        'risk_level',\n" => "",
        "        'capital_requirement',\n" => "",
        "        'employee_requirement',\n" => "",
        "        'requirements',\n" => "",
        "        'meta_keywords',\n" => "",
        "        'created_by',\n" => "",
    ],
    'Media.php' => [
        "        'disk',\n" => "        'application_id',\n",
        "        'filename',\n" => "        'stored_name',\n",
        "        'uploaded_by',\n" => "",
    ],
    'News.php' => [
        "        'category_id',\n" => "        'news_category_id',\n",
        "        'thumbnail_id',\n" => "        'thumbnail',\n",
        "use Illuminate\\Database\\Eloquent\\SoftDeletes;\n" => "",
        "use HasFactory, SoftDeletes;" => "use HasFactory;",
    ],
    'NewsCategory.php' => [
        "        'color',\n" => "",
    ],
    'Permission.php' => [
        "        'module',\n" => "",
    ],
    'Role.php' => [
        "        'description',\n" => "",
    ],
    'User.php' => [
        "use Illuminate\\Database\\Eloquent\\Attributes\\Fillable;" => "use Illuminate\\Database\\Eloquent\\Attributes\\Fillable;\nuse Illuminate\\Database\\Eloquent\\SoftDeletes;",
        "use HasFactory, Notifiable;" => "use HasFactory, Notifiable, SoftDeletes;",
    ]
];

foreach ($replacements as $file => $rules) {
    $path = __DIR__ . '/app/Models/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        foreach ($rules as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        file_put_contents($path, $content);
    }
}
echo "Done patching models\n";
