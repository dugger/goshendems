<?php
/**
 * Template part for displaying elected positions
 *
 * @package Goshen_Dems
 */
?>

<div id="position-<?php echo esc_attr( $args ); ?>" class="elected-position">
	<div class="elected-position__left">
		<h2 class="elected-position__title"><?php echo esc_html(get_the_title($args)); ?></h2>
		<div class="elected-position__description">
			<p><?php echo get_field('description', $args); ?></p>
		</div>
		<?php $requirements = get_field('requirements', $args); ?>
		<?php if (!empty($requirements)) : ?>
			<div class="elected-position__requirements">
				<h3 class="elected-position__requirements-title">Requirements</h3>
				<ul>
					<?php foreach (get_field('requirements', $args) as $requirement) : ?>
						<li><?php echo esc_html($requirement['requirement']); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
	<div class="elected-position__right">
		<?php $salary = get_field('salary', $args); ?>
		<?php if (!empty($salary)) : ?>
			<div class="elected-position__info">
				<div class="elected-position__label">Average Salary</div>
				<div class="elected-position__value"><?php echo esc_html($salary); ?></div>
			</div>
		<?php endif; ?>
		<?php $schedule = get_field('schedule', $args); ?>
		<?php if (!empty($schedule)) : ?>
			<div class="elected-position__info">
				<div class="elected-position__label">Schedule</div>
				<div class="elected-position__value"><?php echo esc_html($schedule); ?></div>
			</div>
		<?php endif; ?>
		<?php $term_length = get_field('term_length', $args); ?>
		<?php if (!empty($term_length)) : ?>
			<div class="elected-position__info">
				<div class="elected-position__label">Term Length</div>
				<div class="elected-position__value"><?php echo esc_html($term_length); ?></div>
			</div>
		<?php endif; ?>
	</div>
</div>
