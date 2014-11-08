function _toAss(s){var a=[];for(var n=0;n<s.length;a[s[n].uid]=s[n].title,n++);return a}
var WORKER_SPISOK=[{uid:8513964,title:"���� �������"},{uid:103928103,title:"������ �������"},{uid:16668390,title:"��� ������"}],
CATEGORY_SPISOK=[{uid:1,title:"����������"},{uid:2,title:"�������"},{uid:3,title:"������������"},{uid:4,title:"������"}],
PERSON_SPISOK=[{uid:1,title:"������� ����"},{uid:12,title:"��"},{uid:13,title:"���"}],
RUBRIC_SPISOK=[{uid:1,title:"������"},{uid:11,title:"�����"},{uid:3,title:"�����"},{uid:7,title:"�����"},{uid:6,title:"����"},{uid:8,title:"������"},{uid:10,title:"���������"},{uid:9,title:"������"}],
RUBRIC_ASS=_toAss(RUBRIC_SPISOK),
INCOME_SPISOK=[{uid:1,title:"��������"},{uid:2,title:"�����������"},{uid:3,title:"�����������"},{uid:4,title:"�� ������� ���"},{uid:5,title:"�� ������� ����"}],
SKIDKA_SPISOK=[{uid:5,title:"5%"},{uid:10,title:"10%"},{uid:15,title:"15%"},{uid:20,title:"20%"},{uid:25,title:"25%"},{uid:30,title:"30%"}],
TXT_LEN_FIRST=30,
TXT_CENA_FIRST=40,
TXT_LEN_NEXT=15,
TXT_CENA_NEXT=15,
OBDOP_SPISOK=[{uid:1,title:"� �����"},{uid:2,title:"������ ���"}],
OBDOP_CENA_ASS={1:40,2:80},
POLOSA_COUNT=[{uid:4,title:4},{uid:6,title:6},{uid:8,title:8},{uid:10,title:10},{uid:12,title:12}],
POLOSA_SPISOK=[{uid:1,title:"������"},{uid:3,title:"���������� �����-�����"},{uid:4,title:"���������� �������"},{uid:2,title:"���������"}],
POLOSA_CENA_ASS={1:24,2:18,3:16,4:18},
POLOSA_NUM={3:1,4:1},
INVOICE_SPISOK=[{uid:1,title:"��������"},{uid:2,title:"����"}],
EXPENSE_SPISOK=[{uid:3,title:"�������� ����������"},{uid:7,title:"�������� ���������� ������"},{uid:5,title:"����. ������"},{uid:1,title:"�������������"},{uid:4,title:"����������"},{uid:6,title:"�/� ���������"},{uid:8,title:"���. ������"},{uid:9,title:"��������"}],
EXPENSE_WORKER={3:1},
COUNTRY_SPISOK=[{uid:1,title:"������"},{uid:2,title:"�������"},{uid:3,title:"��������"},{uid:4,title:"���������"},{uid:5,title:"�����������"},{uid:6,title:"�������"},{uid:7,title:"������"},{uid:8,title:"�������"},{uid:11,title:"����������"},{uid:12,title:"������"},{uid:13,title:"�����"},{uid:14,title:"�������"},{uid:15,title:"�������"},{uid:16,title:"�����������"},{uid:17,title:"���������"},{uid:18,title:"����������"}],
COUNTRY_ASS=_toAss(COUNTRY_SPISOK),
RUBRIC_SUB_SPISOK={1:[{uid:4,title:"�������"},{uid:1,title:"������������"},{uid:9,title:"�����"},{uid:3,title:"������� � ����������"},{uid:6,title:"������"},{uid:7,title:"������"},{uid:12,title:"�����-�����"},{uid:10,title:"�����"},{uid:11,title:"������"},{uid:2,title:"��������������"},{uid:8,title:"�������������"},{uid:5,title:"��������"}]},
RUBRIC_SUB_ASS={0:""};
for(k in RUBRIC_SUB_SPISOK){for(n=0;n<RUBRIC_SUB_SPISOK[k].length;n++){var sp=RUBRIC_SUB_SPISOK[k][n];RUBRIC_SUB_ASS[sp.uid]=sp.title;}}
GN={
311:{week:1,pub:"2012-01-13",txt:"��. 13 ������ 2012",pc:8},
312:{week:2,pub:"2012-01-20",txt:"��. 20 ������ 2012",pc:8},
313:{week:3,pub:"2012-01-27",txt:"��. 27 ������ 2012",pc:8},
314:{week:4,pub:"2012-02-03",txt:"��. 3 ������� 2012",pc:8},
315:{week:5,pub:"2012-02-10",txt:"��. 10 ������� 2012",pc:8},
316:{week:6,pub:"2012-02-17",txt:"��. 17 ������� 2012",pc:8},
317:{week:7,pub:"2012-02-24",txt:"��. 24 ������� 2012",pc:8},
318:{week:8,pub:"2012-03-02",txt:"��. 2 ����� 2012",pc:8},
319:{week:9,pub:"2012-03-09",txt:"��. 9 ����� 2012",pc:8},
320:{week:10,pub:"2012-03-16",txt:"��. 16 ����� 2012",pc:8},
321:{week:11,pub:"2012-03-23",txt:"��. 23 ����� 2012",pc:8},
322:{week:12,pub:"2012-03-30",txt:"��. 30 ����� 2012",pc:8},
323:{week:13,pub:"2012-04-06",txt:"��. 6 ������ 2012",pc:8},
324:{week:14,pub:"2012-04-13",txt:"��. 13 ������ 2012",pc:8},
325:{week:15,pub:"2012-04-20",txt:"��. 20 ������ 2012",pc:8},
326:{week:16,pub:"2012-04-27",txt:"��. 27 ������ 2012",pc:8},
327:{week:17,pub:"2012-05-04",txt:"��. 4 ��� 2012",pc:8},
328:{week:18,pub:"2012-05-11",txt:"��. 11 ��� 2012",pc:8},
329:{week:19,pub:"2012-05-18",txt:"��. 18 ��� 2012",pc:8},
330:{week:20,pub:"2012-05-25",txt:"��. 25 ��� 2012",pc:8},
331:{week:21,pub:"2012-06-01",txt:"��. 1 ���� 2012",pc:8},
332:{week:22,pub:"2012-06-08",txt:"��. 8 ���� 2012",pc:8},
333:{week:23,pub:"2012-06-15",txt:"��. 15 ���� 2012",pc:8},
334:{week:24,pub:"2012-06-22",txt:"��. 22 ���� 2012",pc:8},
335:{week:25,pub:"2012-06-29",txt:"��. 29 ���� 2012",pc:8},
336:{week:26,pub:"2012-07-06",txt:"��. 6 ���� 2012",pc:8},
337:{week:27,pub:"2012-07-13",txt:"��. 13 ���� 2012",pc:8},
338:{week:28,pub:"2012-07-20",txt:"��. 20 ���� 2012",pc:8},
339:{week:29,pub:"2012-07-27",txt:"��. 27 ���� 2012",pc:8},
340:{week:30,pub:"2012-08-03",txt:"��. 3 ������� 2012",pc:8},
341:{week:31,pub:"2012-08-10",txt:"��. 10 ������� 2012",pc:8},
342:{week:32,pub:"2012-08-17",txt:"��. 17 ������� 2012",pc:8},
343:{week:33,pub:"2012-08-24",txt:"��. 24 ������� 2012",pc:8},
344:{week:34,pub:"2012-08-31",txt:"��. 31 ������� 2012",pc:8},
345:{week:35,pub:"2012-09-07",txt:"��. 7 �������� 2012",pc:8},
346:{week:36,pub:"2012-09-14",txt:"��. 14 �������� 2012",pc:8},
347:{week:37,pub:"2012-09-21",txt:"��. 21 �������� 2012",pc:8},
348:{week:38,pub:"2012-09-28",txt:"��. 28 �������� 2012",pc:8},
349:{week:39,pub:"2012-10-05",txt:"��. 5 ������� 2012",pc:8},
350:{week:40,pub:"2012-10-12",txt:"��. 12 ������� 2012",pc:8},
351:{week:41,pub:"2012-10-19",txt:"��. 19 ������� 2012",pc:8},
352:{week:42,pub:"2012-10-26",txt:"��. 26 ������� 2012",pc:8},
353:{week:43,pub:"2012-11-02",txt:"��. 2 ������ 2012",pc:8},
354:{week:44,pub:"2012-11-09",txt:"��. 9 ������ 2012",pc:8},
355:{week:45,pub:"2012-11-16",txt:"��. 16 ������ 2012",pc:8},
356:{week:46,pub:"2012-11-23",txt:"��. 23 ������ 2012",pc:8},
357:{week:47,pub:"2012-11-30",txt:"��. 30 ������ 2012",pc:8},
358:{week:48,pub:"2012-12-07",txt:"��. 7 ������� 2012",pc:8},
359:{week:49,pub:"2012-12-14",txt:"��. 14 ������� 2012",pc:8},
360:{week:50,pub:"2012-12-21",txt:"��. 21 ������� 2012",pc:8},
361:{week:51,pub:"2012-12-28",txt:"��. 28 ������� 2012",pc:8},
362:{week:1,pub:"2013-01-11",txt:"��. 11 ������ 2013",pc:8},
363:{week:2,pub:"2013-01-18",txt:"��. 18 ������ 2013",pc:8},
364:{week:3,pub:"2013-01-25",txt:"��. 25 ������ 2013",pc:8},
365:{week:4,pub:"2013-02-01",txt:"��. 1 ������� 2013",pc:8},
366:{week:5,pub:"2013-02-08",txt:"��. 8 ������� 2013",pc:8},
367:{week:6,pub:"2013-02-15",txt:"��. 15 ������� 2013",pc:8},
368:{week:7,pub:"2013-02-22",txt:"��. 22 ������� 2013",pc:8},
369:{week:8,pub:"2013-03-01",txt:"��. 1 ����� 2013",pc:8},
370:{week:9,pub:"2013-03-08",txt:"��. 8 ����� 2013",pc:8},
371:{week:10,pub:"2013-03-15",txt:"��. 15 ����� 2013",pc:8},
372:{week:11,pub:"2013-03-22",txt:"��. 22 ����� 2013",pc:8},
373:{week:12,pub:"2013-03-29",txt:"��. 29 ����� 2013",pc:8},
374:{week:13,pub:"2013-04-05",txt:"��. 5 ������ 2013",pc:8},
375:{week:14,pub:"2013-04-12",txt:"��. 12 ������ 2013",pc:8},
376:{week:15,pub:"2013-04-19",txt:"��. 19 ������ 2013",pc:8},
377:{week:16,pub:"2013-04-26",txt:"��. 26 ������ 2013",pc:8},
378:{week:17,pub:"2013-05-03",txt:"��. 3 ��� 2013",pc:8},
379:{week:18,pub:"2013-05-10",txt:"��. 10 ��� 2013",pc:8},
380:{week:19,pub:"2013-05-17",txt:"��. 17 ��� 2013",pc:8},
381:{week:20,pub:"2013-05-24",txt:"��. 24 ��� 2013",pc:8},
382:{week:21,pub:"2013-05-31",txt:"��. 31 ��� 2013",pc:8},
383:{week:22,pub:"2013-06-07",txt:"��. 7 ���� 2013",pc:8},
384:{week:23,pub:"2013-06-14",txt:"��. 14 ���� 2013",pc:8},
385:{week:24,pub:"2013-06-21",txt:"��. 21 ���� 2013",pc:8},
386:{week:25,pub:"2013-06-28",txt:"��. 28 ���� 2013",pc:8},
387:{week:26,pub:"2013-07-05",txt:"��. 5 ���� 2013",pc:8},
388:{week:27,pub:"2013-07-12",txt:"��. 12 ���� 2013",pc:8},
389:{week:28,pub:"2013-07-19",txt:"��. 19 ���� 2013",pc:8},
390:{week:29,pub:"2013-07-26",txt:"��. 26 ���� 2013",pc:8},
391:{week:30,pub:"2013-08-02",txt:"��. 2 ������� 2013",pc:8},
392:{week:31,pub:"2013-08-09",txt:"��. 9 ������� 2013",pc:8},
393:{week:32,pub:"2013-08-16",txt:"��. 16 ������� 2013",pc:8},
394:{week:33,pub:"2013-08-23",txt:"��. 23 ������� 2013",pc:8},
395:{week:34,pub:"2013-08-30",txt:"��. 30 ������� 2013",pc:8},
396:{week:35,pub:"2013-09-06",txt:"��. 6 �������� 2013",pc:8},
397:{week:36,pub:"2013-09-13",txt:"��. 13 �������� 2013",pc:8},
398:{week:37,pub:"2013-09-20",txt:"��. 20 �������� 2013",pc:8},
399:{week:38,pub:"2013-09-27",txt:"��. 27 �������� 2013",pc:8},
400:{week:39,pub:"2013-10-04",txt:"��. 4 ������� 2013",pc:8},
401:{week:40,pub:"2013-10-11",txt:"��. 11 ������� 2013",pc:8},
402:{week:41,pub:"2013-10-18",txt:"��. 18 ������� 2013",pc:8},
403:{week:42,pub:"2013-10-25",txt:"��. 25 ������� 2013",pc:8},
404:{week:43,pub:"2013-11-01",txt:"��. 1 ������ 2013",pc:8},
405:{week:44,pub:"2013-11-08",txt:"��. 8 ������ 2013",pc:8},
406:{week:45,pub:"2013-11-15",txt:"��. 15 ������ 2013",pc:8},
407:{week:46,pub:"2013-11-22",txt:"��. 22 ������ 2013",pc:8},
408:{week:47,pub:"2013-11-29",txt:"��. 29 ������ 2013",pc:8},
409:{week:48,pub:"2013-12-06",txt:"��. 6 ������� 2013",pc:8},
410:{week:49,pub:"2013-12-13",txt:"��. 13 ������� 2013",pc:8},
411:{week:50,pub:"2013-12-20",txt:"��. 20 ������� 2013",pc:8},
412:{week:51,pub:"2013-12-27",txt:"��. 27 ������� 2013",pc:8},
413:{week:1,pub:"2014-01-17",txt:"��. 17 ������ 2014",pc:8},
414:{week:2,pub:"2014-01-24",txt:"��. 24 ������ 2014",pc:8},
415:{week:3,pub:"2014-01-31",txt:"��. 31 ������ 2014",pc:8},
416:{week:4,pub:"2014-02-07",txt:"��. 7 ������� 2014",pc:8},
417:{week:5,pub:"2014-02-14",txt:"��. 14 ������� 2014",pc:8},
418:{week:6,pub:"2014-02-21",txt:"��. 21 ������� 2014",pc:8},
419:{week:7,pub:"2014-02-28",txt:"��. 28 ������� 2014",pc:8},
420:{week:8,pub:"2014-03-07",txt:"��. 7 ����� 2014",pc:8},
421:{week:9,pub:"2014-03-14",txt:"��. 14 ����� 2014",pc:8},
422:{week:10,pub:"2014-03-21",txt:"��. 21 ����� 2014",pc:8},
423:{week:11,pub:"2014-03-28",txt:"��. 28 ����� 2014",pc:8},
424:{week:12,pub:"2014-04-04",txt:"��. 4 ������ 2014",pc:8},
425:{week:13,pub:"2014-04-11",txt:"��. 11 ������ 2014",pc:8},
426:{week:14,pub:"2014-04-18",txt:"��. 18 ������ 2014",pc:8},
427:{week:15,pub:"2014-04-25",txt:"��. 25 ������ 2014",pc:8},
428:{week:16,pub:"2014-05-02",txt:"��. 2 ��� 2014",pc:8},
429:{week:17,pub:"2014-05-09",txt:"��. 9 ��� 2014",pc:8},
430:{week:18,pub:"2014-05-16",txt:"��. 16 ��� 2014",pc:8},
431:{week:19,pub:"2014-05-23",txt:"��. 23 ��� 2014",pc:8},
432:{week:20,pub:"2014-05-30",txt:"��. 30 ��� 2014",pc:8},
433:{week:21,pub:"2014-06-06",txt:"��. 6 ���� 2014",pc:8},
434:{week:22,pub:"2014-06-13",txt:"��. 13 ���� 2014",pc:8},
435:{week:23,pub:"2014-06-20",txt:"��. 20 ���� 2014",pc:8},
436:{week:24,pub:"2014-06-27",txt:"��. 27 ���� 2014",pc:8},
437:{week:25,pub:"2014-07-04",txt:"��. 4 ���� 2014",pc:8},
438:{week:26,pub:"2014-07-11",txt:"��. 11 ���� 2014",pc:8},
439:{week:27,pub:"2014-07-18",txt:"��. 18 ���� 2014",pc:8},
440:{week:28,pub:"2014-07-25",txt:"��. 25 ���� 2014",pc:8},
441:{week:29,pub:"2014-08-01",txt:"��. 1 ������� 2014",pc:8},
442:{week:30,pub:"2014-08-08",txt:"��. 8 ������� 2014",pc:8},
443:{week:31,pub:"2014-08-15",txt:"��. 15 ������� 2014",pc:8},
444:{week:32,pub:"2014-08-22",txt:"��. 22 ������� 2014",pc:8},
445:{week:33,pub:"2014-08-29",txt:"��. 29 ������� 2014",pc:8},
446:{week:34,pub:"2014-09-05",txt:"��. 5 �������� 2014",pc:8},
447:{week:35,pub:"2014-09-12",txt:"��. 12 �������� 2014",pc:8},
448:{week:36,pub:"2014-09-19",txt:"��. 19 �������� 2014",pc:8},
449:{week:37,pub:"2014-09-26",txt:"��. 26 �������� 2014",pc:8},
450:{week:38,pub:"2014-10-03",txt:"��. 3 ������� 2014",pc:8},
451:{week:39,pub:"2014-10-10",txt:"��. 10 ������� 2014",pc:8},
452:{week:40,pub:"2014-10-17",txt:"��. 17 ������� 2014",pc:8},
453:{week:41,pub:"2014-10-24",txt:"��. 24 ������� 2014",pc:8},
454:{week:42,pub:"2014-10-31",txt:"��. 31 ������� 2014",pc:8},
455:{week:43,pub:"2014-11-07",txt:"��. 7 ������ 2014",pc:8},
456:{week:44,pub:"2014-11-14",txt:"��. 14 ������ 2014",pc:10},
457:{week:45,pub:"2014-11-21",txt:"��. 21 ������ 2014",pc:8},
458:{week:46,pub:"2014-11-28",txt:"��. 28 ������ 2014",pc:8},
459:{week:47,pub:"2014-12-05",txt:"��. 5 ������� 2014",pc:8},
460:{week:48,pub:"2014-12-12",txt:"��. 12 ������� 2014",pc:8},
461:{week:49,pub:"2014-12-19",txt:"��. 19 ������� 2014",pc:8},
462:{week:50,pub:"2014-12-26",txt:"��. 26 ������� 2014",pc:8}};