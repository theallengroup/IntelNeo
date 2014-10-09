select question_option.id from question_option, question,activity where activity.id = question.activity_id and question.id = question_option.question_id and is_correct='N' and activity_type_id=4;
delete from question_option where id in (21,22,23,24,25,177,178,179,181,196,197,202,204,205,207,209,211,214,215,217,218,220,222,225,226,231,232,234,237,239,241,243);
delete from answer where question_option_id in (21,22,23,24,25,177,178,179,181,196,197,202,204,205,207,209,211,214,215,217,218,220,222,225,226,231,232,234,237,239,241,243);


