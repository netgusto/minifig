<?php

function loadImage($absfilepath) {
	if(file_exists($absfilepath) && is_readable($absfilepath)) {
		return imageCreateFromPng($absfilepath);
	}

	return FALSE;
}

$basepath = realpath('.') . '/assets/sprites/parts-400/';
$hat = intval($_GET["hat"]);
$face = intval($_GET["face"]);
$torso = intval($_GET["torso"]);
$leg = intval($_GET["leg"]);

$hatfile = $basepath . 'hat/hat' . $hat . '.png';
$facefile = $basepath . 'face/face' . $face . '.png';
$torsofile = $basepath . 'torso/torso' . $torso . '.png';
$legfile = $basepath . 'legs/legs' . $leg . '.png';

$hatimg = loadImage($hatfile);
$faceimg = loadImage($facefile);
$torsoimg = loadImage($torsofile);
$legimg = loadImage($legfile);

$sizes = getimagesize($hatfile); $hatwidth = $sizes[0]; $hatheight = $sizes[1];
$sizes = getimagesize($facefile); $facewidth = $sizes[0]; $faceheight = $sizes[1];
$sizes = getimagesize($torsofile); $torsowidth = $sizes[0]; $torsoheight = $sizes[1];
$sizes = getimagesize($legfile); $legwidth = $sizes[0]; $legheight = $sizes[1];

$iTotalWidth = $hatwidth;
$iTotalHeight = $hatheight + $torsoheight + $legheight;	# don't consider face height as it is completely covered by the hat sprite

$rResultImage = imagecreatetruecolor($iTotalWidth, $iTotalHeight);

imagesavealpha($rResultImage, true);
imagealphablending($rResultImage, false);
$transparent = imagecolorallocatealpha($rResultImage, 0, 0, 0, 127);
imagecolortransparent($rResultImage, $transparent);
imagefill($rResultImage, 0, 0, $transparent);

imagealphablending($rResultImage, true);

imagecopyresampled(
	$rResultImage,		# dst_image
	$faceimg,			# src_image
	0,					# dst_x
	0,					# dst_y
	0,					# src_x
	0,					# src_y
	$facewidth,			# dst_w
	$faceheight,			# dst_h
	$facewidth,		# src_w
	$faceheight		# src_h
);

imagecopyresampled(
	$rResultImage,		# dst_image
	$hatimg,			# src_image
	0,					# dst_x
	0,					# dst_y
	0,					# src_x
	0,					# src_y
	$hatwidth,			# dst_w
	$hatheight,			# dst_h
	$hatwidth,		# src_w
	$hatheight		# src_h
);

imagecopyresampled(
	$rResultImage,		# dst_image
	$torsoimg,			# src_image
	0,					# dst_x
	$hatheight,			# dst_y
	0,					# src_x
	0,					# src_y
	$torsowidth,			# dst_w
	$torsoheight,			# dst_h
	$torsowidth,		# src_w
	$torsoheight		# src_h
);

imagecopyresampled(
	$rResultImage,		# dst_image
	$legimg,			# src_image
	0,					# dst_x
	$hatheight + $torsoheight,			# dst_y
	0,					# src_x
	0,					# src_y
	$legwidth,			# dst_w
	$legheight,			# dst_h
	$legwidth,		# src_w
	$legheight		# src_h
);

imageinterlace($rResultImage, 0);
header("Content-type: application/octet-stream");
header('Content-Disposition: attachment; filename="minifig-' . $hat . '-' . $face . '-' . $torso . '-' . $leg . '.png"');
header("Max-age: 0");
$file = sys_get_temp_dir() . '/minifig-' . uniqid() . '.png';
$filetrimmed = sys_get_temp_dir() . '/minifig-' . uniqid() . '-trimmed.png';
ImagePng($rResultImage, $file);
@exec("convert " . escapeshellarg($file) . " -trim " . escapeshellarg($filetrimmed));
fpassthru(fopen($filetrimmed, "r"));
unlink($file);
unlink($filetrimmed);