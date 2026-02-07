-- Filter published posts that have custom field 'photos' = 0 and 'videos' = 0
-- Author: Dicky Ibrohim

SELECT * FROM wp_posts
WHERE post_type = 'post'
AND post_status = 'publish'
AND ID IN (
    SELECT post_id FROM wp_postmeta
    WHERE meta_key = 'photos'
    AND meta_value = '0'
)
AND ID IN (
    SELECT post_id FROM wp_postmeta
    WHERE meta_key = 'videos'
    AND meta_value = '0'
);
